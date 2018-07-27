<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\ORM\QueryBuilder;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{
    const CORRECT_FIELD_NAMES = ['idd' => 'id'];
    protected $SORT_FIELDS = ['id' => 'id', 'content' => 'content', 'contentType' => 'contentType', 'raw' => 'raw', 'createdDate' => 'createdDate', 'modified' => 'modified',
        'product' => 'subject', 'author' => 'name'
    ];

    const FIND_EXACTLY_FIELDS = ['businessRule', 'seller', 'businessSetting', 'mode', 'visibility'];

    public function getCommentsForUserQuery(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = array(
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.content', ':searchKey'),
                $qb->expr()->like('a.contentType', ':searchKey'),
                $qb->expr()->like('a.raw', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
                $qb->expr()->like('a.modified', ':searchKey'),
            );
            $orX->addMultiple($conditions);

            $qb
                ->andWhere($orX)
                ->setParameter('searchKey', $searchLike);

            $searchLike = sprintf('%%%s%%', str_replace("/", "-", $param->getSearchKey()));
            $qb
                ->orWhere($qb->expr()->like('SUBSTRING(a.createdDate, 0, 10)', ':date'))
                ->setParameter('date', $searchLike);
        }

        $qb = $this->addFilters($qb, $param);

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            array_key_exists($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case 'seller':
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
        return $user instanceof AccountInterface ? $this->getCommentsForAccountQuery($user) : $this->createQueryBuilder('a')->leftJoin('a.seller' ,'p');
    }

    public function getCommentsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.author', 'p')
            ->select('a, p')
            ->where('a.author = :account')
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
        foreach ($searches as $searchField => $searchLike) {
            if (is_null($searchLike) || strlen($searchLike) < 1) {
                continue;
            }

            $searchField = array_key_exists($searchField, self::CORRECT_FIELD_NAMES) ? self::CORRECT_FIELD_NAMES[$searchField] : $searchField;

            if (in_array($searchField, self::FIND_EXACTLY_FIELDS)) {
                if (count(explode(",", $searchLike)) < 2) {
                    //Simple filter: value
                    $qb
                        ->andWhere(sprintf("a.%s = :%s", $searchField, $searchField))
                        ->setParameter(sprintf('%s', $searchField), $searchLike);
                } else {
                    //Multi filter: value1, value2, value3
                    $searchLikes = explode(",", $searchLike);
                    $searchLikes = array_map(function ($search) {
                        return sprintf('%s', trim($search));
                    }, $searchLikes);

                    $orX = $qb->expr()->orX();
                    $conditions = array(
                        $qb->expr()->in(sprintf("a.%s", $searchField), $searchLikes),
                    );
                    $orX->addMultiple($conditions);

                    $qb
                        ->andWhere($orX);
                }
            } else {
                $searchLike = sprintf('%%%s%%', $searchLike);

                $orX = $qb->expr()->orX();
                $conditions = array(
                    $qb->expr()->like(sprintf("a.%s", $searchField), sprintf(":%s", $searchField)),
                );
                $orX->addMultiple($conditions);

                $qb
                    ->andWhere($orX)
                    ->setParameter(sprintf('%s', $searchField), $searchLike);
            }
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function findCommentsByProduct($user, ProductInterface $product, $page, $limit)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere('a.product = :product')
            ->setParameter('product', $product);

        $qb->addOrderBy('a.createdDate', 'desc');

        if ($product->getCommentVisibility() == ProductInterface::VISIBILITY_PRIVATE &&
            $user instanceof AccountInterface && $product->getSeller()->getId() != $user->getId()) {
            $qb
                ->andWhere('a.author = :author')
                ->setParameter('author', $user);
        }

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
    public function findCommentsByComment(CommentInterface $comment, $page, $limit) {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere('a.masterComment = :comment')
            ->setParameter('comment', $comment);

        $qb->addOrderBy('a.createdDate', 'desc');

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
    public function findTotalCommentsCountByProduct(ProductInterface $product)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select("count(a.id)")
            ->andWhere('a.product = :product')
            ->setParameter('product', $product);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function findTotalCommentsCountByComment(CommentInterface $comment)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select("count(a.id)")
            ->andWhere('a.masterComment = :comment')
            ->setParameter('comment', $comment);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
