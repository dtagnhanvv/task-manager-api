<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\ORM\QueryBuilder;

class ReactionRepository extends EntityRepository implements ReactionRepositoryInterface
{
    const CORRECT_FIELD_NAMES = ['idd' => 'id'];
    protected $SORT_FIELDS = ['id' => 'id', 'emotion' => 'emotion', 'createdDate' => 'createdDate'];

    const FIND_EXACTLY_FIELDS = ['id', 'emotion', 'product', 'comment'];

    /**
     * @inheritdoc
     */
    public function findReactionsByProduct(ProductInterface $product, $page, $limit)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere('a.product = :product')
            ->setParameter('product', $product);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($page) && $page > 0) {
            $offset = ($page - 1) * $limit;
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function findReactionsByComment(CommentInterface $comment, $page, $limit)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere('a.comment = :comment')
            ->setParameter('comment', $comment);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($page) && $page > 0) {
            $offset = ($page - 1) * $limit;
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getReactionsForUserQuery(UserRoleInterface $user, PagerParam $param)
    {
//        $qb = $this->createQueryBuilderForUser($user);
        $qb = $this->createQueryBuilderForAllUser();

        if (is_string($param->getSearchKey())) {
            $searchReaction = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = array(
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.emotion', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
            );
            $orX->addMultiple($conditions);

            $qb
                ->andWhere($orX)
                ->setParameter('searchKey', $searchReaction);

            $searchReaction = sprintf('%%%s%%', str_replace("/", "-", $param->getSearchKey()));
            $qb
                ->orWhere($qb->expr()->like('SUBSTRING(a.createdDate, 0, 10)', ':date'))
                ->setParameter('date', $searchReaction);
        }

        $qb = $this->addFilters($qb, $param);

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            array_key_exists($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case 'viewer':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                default:
                    $qb->addOrderBy('a.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
            }
        } else {
            $qb->addOrderBy('a.createdDate', 'desc');
        }

        return $qb;
    }

    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof AccountInterface ? $this->getReactionsForAccountQuery($user) : $this->createQueryBuilder('a')->leftJoin('a.viewer' ,'p');
    }

    private function createQueryBuilderForAllUser() {
        return $this->createQueryBuilder('a')->leftJoin('a.viewer' ,'p');
    }
    public function getReactionsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.viewer', 'p')
            ->select('a, p')
            ->where('a.viewer = :account')
            ->setParameter('account', $account);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param PagerParam $param
     * @return QueryBuilder
     */
    private function addFilters(QueryBuilder $qb, PagerParam $param)
    {
        $searches = $param->getSearches();
        foreach ($searches as $searchField => $searchReaction) {
            if (is_null($searchReaction) || strlen($searchReaction) < 1) {
                continue;
            }

            $searchField = array_key_exists($searchField, self::CORRECT_FIELD_NAMES) ? self::CORRECT_FIELD_NAMES[$searchField] : $searchField;

            if (in_array($searchField, self::FIND_EXACTLY_FIELDS)) {
                if (count(explode(",", $searchReaction)) < 2) {
                    //Simple filter: value
                    $qb
                        ->andWhere(sprintf("a.%s = :%s", $searchField, $searchField))
                        ->setParameter(sprintf('%s', $searchField), $searchReaction);
                } else {
                    //Multi filter: value1, value2, value3
                    $searchReactions = explode(",", $searchReaction);
                    $searchReactions = array_map(function ($search) {
                        return sprintf('%s', trim($search));
                    }, $searchReactions);

                    $orX = $qb->expr()->orX();
                    $conditions = array(
                        $qb->expr()->in(sprintf("a.%s", $searchField), $searchReactions),
                    );
                    $orX->addMultiple($conditions);

                    $qb
                        ->andWhere($orX);
                }
            } else {
                $searchReaction = sprintf('%%%s%%', $searchReaction);

                $orX = $qb->expr()->orX();
                $conditions = array(
                    $qb->expr()->like(sprintf("a.%s", $searchField), sprintf(":%s", $searchField)),
                );
                $orX->addMultiple($conditions);

                $qb
                    ->andWhere($orX)
                    ->setParameter(sprintf('%s', $searchField), $searchReaction);
            }
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByComment(CommentInterface $comment, $page = 1, $limit = 1)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select("count(a.id)")
            ->andWhere('a.comment = :comment')
            ->andWhere('a.emotion is not null')
            ->setParameter('comment', $comment);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param CommentInterface $comment
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findReactionEmotionsByComment(CommentInterface $comment, $page = 1, $limit = 1)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select('a.emotion')
            ->distinct(true)
            ->andWhere('a.comment = :comment')
            ->setParameter('comment', $comment);

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByProduct(ProductInterface $product, $page = 1, $limit = 1)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select("count(a.id)")
            ->andWhere('a.product = :product')
            ->andWhere('a.emotion is not null')
            ->setParameter('product', $product);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByProductGroupByEmotion(ProductInterface $product)
    {
        $sql = sprintf("SELECT emotion, count(id) as count FROM `core_reaction` WHERE product_id = %s GROUP BY (emotion)", $product->getId());
        $connection = $this->getEntityManager()->getConnection();

        $rows = $connection->executeQuery($sql)->fetchAll();
        $result = [];

        foreach ($rows as $row) {
            if (!is_array($row) || !array_key_exists('emotion', $row) || !array_key_exists('count', $row)) {
                continue;
            }
            $result[$row['emotion']] = floatval($row['count']);
        }

        arsort($result);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByCommentGroupByEmotion(CommentInterface $comment)
    {
        $sql = sprintf("SELECT emotion, count(id) as count FROM `core_reaction` WHERE comment_id = %s GROUP BY (emotion)", $comment->getId());
        $connection = $this->getEntityManager()->getConnection();

        $rows = $connection->executeQuery($sql)->fetchAll();
        $result = [];

        foreach ($rows as $row) {
            if (!is_array($row) || !array_key_exists('emotion', $row) || !array_key_exists('count', $row)) {
                continue;
            }
            $result[$row['emotion']] = floatval($row['count']);
        }

        arsort($result);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function findReactionByUserAndObject(AccountInterface $user, $type, $object)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere('a.viewer = :viewer')
            ->setParameter('viewer', $user);
        $where = sprintf("a.%s = :%s", $type, $type);

        $qb
            ->andWhere($where)
            ->setParameter($type, $object);

        $result =  $qb->getQuery()->getResult();

        return reset($result);
    }
}
