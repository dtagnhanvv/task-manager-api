<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\TagInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\ORM\QueryBuilder;

class TagRepository extends EntityRepository implements TagRepositoryInterface
{
    const CORRECT_FIELD_NAMES = ['idd' => 'id'];
    protected $SORT_FIELDS = ['id' => 'id', 'name' => 'name', 'type' => 'type', 'url' => 'url', 'createdDate' => 'createdDate'];

    const FIND_EXACTLY_FIELDS = [];

    public function getTagsForUserQuery(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = array(
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.name', ':searchKey'),
                $qb->expr()->like('a.url', ':searchKey'),
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
//        return $user instanceof AccountInterface ? $this->getTagsForAccountQuery($user) : $this->createQueryBuilder('a')->leftJoin('a.seller' ,'p');
        return $this->createQueryBuilder('a');
    }

    public function getTagsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
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
    public function findTagsByProduct(ProductInterface $product, $page, $limit)
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
    public function findTagsByTag(TagInterface $tag, $page, $limit) {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere('a.masterTag = :tag')
            ->setParameter('tag', $tag);

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
    public function findTotalTagsCountByProduct(ProductInterface $product)
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
    public function findTotalTagsCountByTag(TagInterface $tag)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select("count(a.id)")
            ->andWhere('a.masterTag = :tag')
            ->setParameter('tag', $tag);

        return $qb->getQuery()->getSingleScalarResult();
    }
}