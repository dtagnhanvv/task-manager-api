<?php

namespace Biddy\Repository\Core;

use Biddy\Model\User\Role\AdminInterface;
use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\ORM\QueryBuilder;

class CreditTransactionRepository extends EntityRepository implements CreditTransactionRepositoryInterface
{
    const SORT_FIELDS = ['id' => 'id', 'amount' => 'amount', 'type' => 'type', 'detail' => 'detail'];
    const MAPPING = ['fromWallet' => 'u.name', 'targetWallet' => 'u.name'];
    const CORRECT_FIELD_NAMES = ['idd' => 'id'];
    const FIND_EXACTLY_FIELDS = [];
    const CORRECT_FIELDS_MAPPING = [];

    /**
     * @inheritdoc
     */
    public function getCreditTransactionsForUserQuery(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_string($param->getSearchKey()) && !empty($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = array(
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.amount', ':searchKey'),
                $qb->expr()->like('a.type', ':searchKey'),
                $qb->expr()->like('a.detail', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
                $qb->expr()->like('f.username', ':searchKey'),
                $qb->expr()->like('f.id', ':searchKey'),
                $qb->expr()->like('t.username', ':searchKey'),
                $qb->expr()->like('t.id', ':searchKey')
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

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            array_key_exists($param->getSortField(), self::SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case 'fromWallet':
                    $qb->addOrderBy(self::MAPPING[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'targetWallet':
                    $qb->addOrderBy(self::MAPPING[$param->getSortField()], $param->getSortDirection());
                    break;
                default:
                    $qb->addOrderBy('a.' . $param->getSortField(), $param->getSortDirection());
                    break;
            }
        } else {
            $qb->addOrderBy('a.createdDate', 'desc');
        }

        $qb = $this->addFilters($qb, $param, $user);

        return $qb;
    }

    /**
     * @param AccountInterface $account
     * @param null $limit
     * @param null $offset
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCreditTransactionsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.fromWallet', 'u')
            ->select('a, u')
            ->where('a.fromWallet = :fromWallet')
            ->setParameter('fromWallet', $account);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * @param UserRoleInterface $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.fromWallet', 'f')
            ->leftJoin('f.owner', 'fo')
            ->leftJoin('a.targetWallet', 't')
            ->leftJoin('t.owner', 'to');

        $qb = $this->addQueryForMultiUser($qb, $user);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $user
     * @return QueryBuilder
     */
    private function addQueryForMultiUser(QueryBuilder $qb, UserRoleInterface $user)
    {
        $orX = $qb->expr()->orX();
        $conditions = [];
        $andX = $qb->expr()->orX();
        $conditionPrivates[] = $qb->expr()->eq(sprintf("f.%s", 'owner'), sprintf("'%s'", $user->getId()));
        $conditionPrivates[] = $qb->expr()->eq(sprintf("t.%s", 'owner'), sprintf("'%s'", $user->getId()));
        $andX->addMultiple($conditionPrivates);
        $conditions[] = $andX;

        $orX->addMultiple($conditions);
        $qb->andWhere($orX);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param PagerParam $param
     * @param $user
     * @return QueryBuilder
     */
    private function addFilters(QueryBuilder $qb, PagerParam $param, $user)
    {
        $searches = $param->getSearches();

        foreach ($searches as $searchField => $searchLike) {
            if (is_null($searchLike) || strlen($searchLike) < 1) {
                continue;
            }

            $searchField = $this->reformatSearchField($searchField);

            if ($searchField == 'createdDate') {
                $qb = $this->addSpecificFilterForCreatedDate($qb, $searchLike);
                continue;
            }

            if (in_array($searchField, self::FIND_EXACTLY_FIELDS)) {
                $qb = $this->filterExactlyFields($qb, $searchField, $searchLike);
            } else {
                $qb = $this->filterLikeFields($qb, $searchField, $searchLike);
            }
        }

        return $qb;
    }

    /**
     * @param $searchField
     * @return mixed
     */
    private function reformatSearchField($searchField)
    {
        $searchField = array_key_exists($searchField, self::CORRECT_FIELD_NAMES) ? self::CORRECT_FIELD_NAMES[$searchField] : $searchField;

        return $searchField;
    }

    /**
     * @param QueryBuilder $qb
     * @param $dateRange
     * @return QueryBuilder
     */
    private function addSpecificFilterForCreatedDate(QueryBuilder $qb, $dateRange)
    {
        if (!is_array($dateRange)) {
            $dateRange = explode(",", $dateRange);
        }

        $startDate = new \DateTime(reset($dateRange));
        $endDate = new \DateTime(end($dateRange));

        if ($startDate->format("Y-m-d H:i") == $endDate->format("Y-m-d H:i")) {
            $startDate->setTime(0, 0);
            $endDate->setTime(23, 59);
        }

        $qb
            ->andWhere('a.createdDate >= :startDate')
            ->andWhere('a.createdDate <= :endDate')
            ->setParameter('startDate', $startDate->format("Y-m-d H:i:s"))
            ->setParameter('endDate', $endDate->format("Y-m-d H:i:s"));

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $searchField
     * @param $searchLike
     * @return QueryBuilder
     */
    private function filterExactlyFields(QueryBuilder $qb, $searchField, $searchLike)
    {
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

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $searchField
     * @param $searchLike
     * @return QueryBuilder
     */
    private function filterLikeFields(QueryBuilder $qb, $searchField, $searchLike)
    {
        $searchLike = sprintf('%%%s%%', $searchLike);

        $orX = $qb->expr()->orX();
        if (array_key_exists($searchField, self::CORRECT_FIELDS_MAPPING)) {
            $conditions = array(
                $qb->expr()->like(sprintf(self::CORRECT_FIELDS_MAPPING[$searchField]), sprintf(":%s", $searchField)),
            );
        } else {
            $conditions = array(
                $qb->expr()->like(sprintf("a.%s", $searchField), sprintf(":%s", $searchField)),
            );
        }

        $orX->addMultiple($conditions);

        $qb
            ->andWhere($orX)
            ->setParameter(sprintf('%s', $searchField), $searchLike);

        return $qb;
    }
}
