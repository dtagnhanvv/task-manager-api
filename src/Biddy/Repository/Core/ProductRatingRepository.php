<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class ProductRatingRepository extends EntityRepository implements ProductRatingRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'createdDate' => 'createdDate', 'rateValue' => 'rateValue', 'rateMessage' => 'rateMessage'];

    /**
     * @inheritdoc
     */
    public function getProductRatingsForUserQuery(UserRoleInterface $user, $product, $bill, PagerParam $param)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if ($product instanceof ProductInterface) {
            $qb
                ->andWhere('a.product = :product')
                ->setParameter('product', $product);
        }

        if ($bill instanceof BillInterface) {
            $qb
                ->andWhere('a.bill = :bill')
                ->setParameter('bill', $bill);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = array(
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.rateValue', ':searchKey'),
                $qb->expr()->like('a.rateMessage', ':searchKey')
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
            array_key_exists($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case 'product':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'reviewer':
                    $qb->addOrderBy('b.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
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

    /**
     * @param AccountInterface $account
     * @param null $limit
     * @param null $offset
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductRatingsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.reviewer', 'p')
            ->select('a, p')
            ->where('a.reviewer = :reviewer')
            ->setParameter('reviewer', $account);

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
        return $user instanceof AccountInterface ? $this->getProductRatingsForAccountQuery($user) :
            $this->createQueryBuilder('a')
                ->leftJoin('a.reviewer', 'b')
                ->leftJoin('a.product', 'p');
    }

    /**
     * @inheritdoc
     */
    public function getProductRatingForProductQuery(ProductInterface $product, PagerParam $pagerParam)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.product = :product')
            ->setParameter('product', $product);
        
        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getProductRatingForBillQuery(UserRoleInterface $user, BillInterface $bill)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.bill = :bill')
            ->andWhere('a.reviewer = :reviewer')
            ->setParameter('bill', $bill)
            ->setParameter('reviewer', $user);

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function findTotalProductRatingByProduct(ProductInterface $product)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select("count(a.id)")
            ->andWhere('a.product = :product')
            ->andWhere('a.rateValue is not null')
            ->setParameter('product', $product);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function findDetailRatingByProduct(ProductInterface $product)
    {
        $sql = sprintf("SELECT rate_value, count(id) as count FROM `core_product_rating` WHERE product_id = %s GROUP BY (rate_value)", $product->getId());
        $connection = $this->getEntityManager()->getConnection();

        $rows = $connection->executeQuery($sql)->fetchAll();
        $result = [];

        foreach ($rows as $row) {
            if (!is_array($row) || !array_key_exists('rate_value', $row) || !array_key_exists('count', $row)) {
                continue;
            }
            $result[$row['rate_value']] = floatval($row['count']);
        }

        arsort($result);

        return $result;
    }
}
