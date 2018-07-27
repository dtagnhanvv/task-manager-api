<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class BillRepository extends EntityRepository implements BillRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'createdDate' => 'createdDate', 'price' => 'price', 'payment' => 'payment', 'noteForSeller' => 'noteForSeller',
        'buyer' => 'firstName', 'seller' => 'firstName', 'bid' => 'bid'
    ];

    const CORRECT_FIELD_NAMES = [];
    const FIND_EXACTLY_FIELDS = [];
    const CORRECT_FIELDS_MAPPING = [];

    public function getBillsForUserQuery(UserRoleInterface $user, PagerParam $param, $params = [])
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = array(
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
                $qb->expr()->like('a.price', ':searchKey'),
                $qb->expr()->like('a.payment', ':searchKey'),
                $qb->expr()->like('a.noteForSeller', ':searchKey'),
                $qb->expr()->like('p.username', ':searchKey'),
                $qb->expr()->like('s.username', ':searchKey')
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

        $status = isset($params['status']) ? $params['status'] : null;
        $billGroup = isset($params['billGroup']) ? $params['billGroup'] : null;

        $qb = $this->filterByStatus($status, $billGroup, $qb, $user);

        $qb = $this->addFilters($qb, $param, $user);

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            array_key_exists($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case 'buyer':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'bids':
                    $qb->addOrderBy('o.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                default:
                    $qb->addOrderBy('a.' . $param->getSortField(), $param->getSortDirection());
                    break;
            }
        } else {
            $qb->addOrderBy('a.createdDate', 'desc');
        }

        return $qb;
    }

    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof AccountInterface ? $this->getBillsForAccountQuery($user) :
            $this->createQueryBuilder('a')
                ->leftJoin('a.buyer', 'p')
                ->leftJoin('a.seller', 's')
                ->leftJoin('a.bid', 'o');
    }

    public function getBillsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.buyer', 'p')
            ->leftJoin('a.seller', 's')
            ->leftJoin('a.bid', 'o')
            ->select('a, p')
            ->where('a.buyer = :account')
            ->orWhere('a.seller = :account')
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

            $searchField = array_key_exists($searchField, self::CORRECT_FIELD_NAMES) ? self::CORRECT_FIELD_NAMES[$searchField] : $searchField;

            if (in_array($searchField, self::FIND_EXACTLY_FIELDS)) {
                if (count(explode(",", $searchLike)) < 2) {
                    //Simple filter: value
                    $qb
                        ->andWhere(sprintf("%s = :%s", $searchField, $searchField))
                        ->setParameter(sprintf('%s', $searchField), $searchLike);
                } else {
                    //Multi filter: value1, value2, value3
                    $searchLikes = explode(",", $searchLike);
                    $searchLikes = array_map(function ($search) {
                        return sprintf('%s', trim($search));
                    }, $searchLikes);

                    $orX = $qb->expr()->orX();
                    $conditions = [
                        $qb->expr()->in($searchField, $searchLikes),
                    ];
                    $orX->addMultiple($conditions);

                    $qb
                        ->andWhere($orX);
                }
            } else {
                $searchLike = sprintf('%%%s%%', $searchLike);

                $orX = $qb->expr()->orX();
                if (array_key_exists($searchField, self::CORRECT_FIELDS_MAPPING)) {
                    $conditions = [
                        $qb->expr()->like(sprintf(self::CORRECT_FIELDS_MAPPING[$searchField]), sprintf(":%s", $searchField)),
                    ];
                } else {
                    $conditions = [
                        $qb->expr()->like($searchField, sprintf(":%s", $searchField)),
                    ];
                }

                $orX->addMultiple($conditions);

                $qb
                    ->andWhere($orX)
                    ->setParameter($searchField, $searchLike);
            }
        }

        return $qb;
    }

    /**
     * @param $status
     * @param $billGroup
     * @param QueryBuilder $qb
     * @param $user
     * @return QueryBuilder
     */
    private function filterByStatus($status, $billGroup, QueryBuilder $qb, $user)
    {
        if (empty($status)) {
            return $qb;
        }

        $qb
            ->andWhere('a.status = :status')
            ->setParameter('status', $status);

        if ($user instanceof AdminInterface || $user instanceof SaleInterface) {
            return $qb;
        }

        $qb
            ->leftJoin('o.auction', 'au')
            ->leftJoin('au.product', 'product');

        $rollNeedConfirm = [ProductInterface::BUSINESS_SETTINGS_BUY, ProductInterface::BUSINESS_SETTINGS_RENT];
        $roleConfirm = [ProductInterface::BUSINESS_SETTINGS_SELL, ProductInterface::BUSINESS_SETTINGS_LEASE];

        $orX = $qb->expr()->orX();

        if ($billGroup == BillInterface::GROUP_NEED_CONFIRMED) {
            //If is buyer and product business is sell, rent
            $conditionPrivates = [];
            $miniAnd1 = $qb->expr()->andX();
            $conditionPrivates[] = $qb->expr()->eq('a.buyer', $user->getId());
            $conditionPrivates[] = $qb->expr()->in('product.businessSetting', $roleConfirm);
            $miniAnd1->addMultiple($conditionPrivates);

            //Or is seller and product business is sell, lease
            $conditionPrivates = [];
            $miniAnd2 = $qb->expr()->andX();
            $conditionPrivates[] = $qb->expr()->eq('a.seller', $user->getId());
            $conditionPrivates[] = $qb->expr()->in('product.businessSetting', $rollNeedConfirm);
            $miniAnd2->addMultiple($conditionPrivates);
            
            $orX->addMultiple([$miniAnd1, $miniAnd2]);
        } else if ($billGroup == BillInterface::GROUP_WAIT_CONFIRMED) {
            //If is seller and product business is buy, rent
            $conditionPrivates = [];
            $miniAnd1 = $qb->expr()->andX();
            $conditionPrivates[] = $qb->expr()->eq('a.seller', $user->getId());
            $conditionPrivates[] = $qb->expr()->in('product.businessSetting', $roleConfirm);
            $miniAnd1->addMultiple($conditionPrivates);

            //Or is buyer and product business is sell, lease
            $conditionPrivates = [];
            $miniAnd2 = $qb->expr()->andX();
            $conditionPrivates[] = $qb->expr()->eq('a.buyer', $user->getId());
            $conditionPrivates[] = $qb->expr()->in('product.businessSetting', $rollNeedConfirm);
            $miniAnd2->addMultiple($conditionPrivates);

            $orX->addMultiple([$miniAnd1, $miniAnd2]);
        }

        $qb->andWhere($orX);

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getSummaryBillStatus(UserRoleInterface $user, $params)
    {
        $billGroup = isset($params['billGroup']) ? $params['billGroup'] : null;
        $supportStatus = BillInterface::SUPPORT_STATUS;
        $result = [];

        foreach ($supportStatus as $status) {
            $result[$status] = $this->getBillStatus($user, $status, $billGroup);
        }

        return $result;
    }

    /**
     * @param $user
     * @param null $status
     * @param null $billGroup
     * @return mixed
     */
    private function getBillStatus($user, $status = null, $billGroup = null)
    {
        $qb = $this->createQueryBuilderForUser($user);
        $qb = $this->filterByStatus($status, $billGroup, $qb, $user);

        $qb->select('count(a.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
