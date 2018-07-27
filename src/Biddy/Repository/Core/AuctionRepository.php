<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Service\Util\PublicSimpleException;
use Doctrine\ORM\EntityRepository;

class AuctionRepository extends EntityRepository implements AuctionRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id',
        'minimumPrice' => 'minimumPrice',
        'showBid' => 'showBid',
        'status' => 'status',
        'startDate' => 'startDate',
        'endDate' => 'endDate',
        'type' => 'type',
        'objective' => 'objective',
        'incrementType' => 'incrementType',
        'incrementValue' => 'incrementValue',
        'payment' => 'payment',
    ];

    /**
     * @inheritdoc
     */
    public function getAuctionsForUserQuery(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.product', 'p')
            ->where('p.seller = :seller')
            ->setParameter('seller', $user);

        if (is_string($param->getSearchKey()) && !empty($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.type', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
                $qb->expr()->like('a.objective', ':searchKey'),
                $qb->expr()->like('a.incrementType', ':searchKey'),
                $qb->expr()->like('a.incrementValue', ':searchKey'),
                $qb->expr()->like('t.name', ':searchKey'),
                $qb->expr()->like('p.username', ':searchKey'),
                $qb->expr()->like('p.phone', ':searchKey'),
                $qb->expr()->like('p.firstName', ':searchKey'),
                $qb->expr()->like('p.city', ':searchKey'),
                $qb->expr()->like('p.address', ':searchKey'),
                $qb->expr()->like('p.emailSendAlert', ':searchKey'),
                $qb->expr()->like('p.rating', ':searchKey'),
                $qb->expr()->like('r.rateValue', ':searchKey'),
            ];
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
                case 'seller':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'productTags':
                    $qb->addOrderBy('t.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'rating':
                    $qb->addOrderBy('r.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
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

    /**
     * @inheritdoc
     */
    public function getAuctionForProductQuery(ProductInterface $product, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.product = :product')
            ->setParameter('product', $product);

        if (is_string($param->getSearchKey()) && !empty($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.subject', ':searchKey'),
                $qb->expr()->like('a.summary', ':searchKey'),
                $qb->expr()->like('a.detail', ':searchKey'),
                $qb->expr()->like('a.address', ':searchKey'),
                $qb->expr()->like('a.mode', ':searchKey'),
                $qb->expr()->like('a.visibility', ':searchKey'),
                $qb->expr()->like('a.businessSetting', ':searchKey'),
                $qb->expr()->like('a.businessRule', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
                $qb->expr()->like('t.name', ':searchKey'),
                $qb->expr()->like('p.username', ':searchKey'),
                $qb->expr()->like('p.phone', ':searchKey'),
                $qb->expr()->like('p.firstName', ':searchKey'),
                $qb->expr()->like('p.city', ':searchKey'),
                $qb->expr()->like('p.address', ':searchKey'),
                $qb->expr()->like('p.emailSendAlert', ':searchKey'),
                $qb->expr()->like('p.rating', ':searchKey'),
                $qb->expr()->like('r.rateValue', ':searchKey'),
            ];
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
                case 'seller':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'productTags':
                    $qb->addOrderBy('t.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'rating':
                    $qb->addOrderBy('r.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
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

    /**
     * @inheritdoc
     */
    public function getActiveAuctionForProduct(ProductInterface $product, \DateTime $date)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.product = :product')
            ->andWhere('a.status != :status')
            ->andWhere('a.startDate <= :currentDate')
            ->andWhere('a.endDate >= :currentDate')
            ->setParameter('product', $product)
            ->setParameter('status', AuctionInterface::STATUS_CLOSED)
            ->setParameter('currentDate', $date->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'))->format("Y-m-d H:i:s"))
            ->orderBy('a.id', 'desc');

        $auctions = $qb->getQuery()->getResult();

        if (empty($auctions)) {
            throw new PublicSimpleException('Not have any active auction');
        }

        return end($auctions);
    }

    /**
     * @inheritdoc
     */
    public function getActiveProductsBiddingQuery(UserRoleInterface $account, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('u')
            ->distinct(true)
            ->leftJoin('u.bids', 'b')
            ->leftJoin('u.product', 'a')
            ->leftJoin('a.seller', 'p')
            ->leftJoin('a.productTags', 'pt')
            ->leftJoin('a.productRatings', 'r')
            ->leftJoin('pt.tag', 't');

        if ($account instanceof AccountInterface) {
            $qb
                ->where('a.seller = :seller')
                ->setParameter('seller', $account);
        }

        if (is_string($param->getSearchKey()) && !empty($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.subject', ':searchKey'),
                $qb->expr()->like('a.summary', ':searchKey'),
                $qb->expr()->like('a.detail', ':searchKey'),
                $qb->expr()->like('a.address', ':searchKey'),
                $qb->expr()->like('a.mode', ':searchKey'),
                $qb->expr()->like('a.visibility', ':searchKey'),
                $qb->expr()->like('a.businessSetting', ':searchKey'),
                $qb->expr()->like('a.businessRule', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
                $qb->expr()->like('t.name', ':searchKey'),
                $qb->expr()->like('p.username', ':searchKey'),
                $qb->expr()->like('p.phone', ':searchKey'),
                $qb->expr()->like('p.firstName', ':searchKey'),
                $qb->expr()->like('p.city', ':searchKey'),
                $qb->expr()->like('p.address', ':searchKey'),
                $qb->expr()->like('p.emailSendAlert', ':searchKey'),
                $qb->expr()->like('p.rating', ':searchKey'),
                $qb->expr()->like('r.rateValue', ':searchKey'),
            ];
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
                case 'seller':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'productTags':
                    $qb->addOrderBy('t.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'rating':
                    $qb->addOrderBy('r.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                default:
                    $qb->addOrderBy('u.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
            }
        } else {
            $qb->addOrderBy('u.startDate', 'desc');
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getAuctionsForUserBiddingQuery(UserRoleInterface $account, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('u')
            ->distinct(true)
            ->leftJoin('u.product', 'a')
            ->leftJoin('u.bids', 'b')
            ->leftJoin('a.seller', 'p')
            ->leftJoin('a.productTags', 'pt')
            ->leftJoin('a.productRatings', 'r')
            ->leftJoin('pt.tag', 't');

        if ($account instanceof AccountInterface) {
            $qb
                ->where('b.buyer = :buyer')
                ->setParameter('buyer', $account);
        }

        if (is_string($param->getSearchKey()) && !empty($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.subject', ':searchKey'),
                $qb->expr()->like('a.summary', ':searchKey'),
                $qb->expr()->like('a.detail', ':searchKey'),
                $qb->expr()->like('a.address', ':searchKey'),
                $qb->expr()->like('a.mode', ':searchKey'),
                $qb->expr()->like('a.visibility', ':searchKey'),
                $qb->expr()->like('a.businessSetting', ':searchKey'),
                $qb->expr()->like('a.businessRule', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey'),
                $qb->expr()->like('t.name', ':searchKey'),
                $qb->expr()->like('p.username', ':searchKey'),
                $qb->expr()->like('p.phone', ':searchKey'),
                $qb->expr()->like('p.firstName', ':searchKey'),
                $qb->expr()->like('p.city', ':searchKey'),
                $qb->expr()->like('p.address', ':searchKey'),
                $qb->expr()->like('p.emailSendAlert', ':searchKey'),
                $qb->expr()->like('p.rating', ':searchKey'),
                $qb->expr()->like('r.rateValue', ':searchKey'),
            ];
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
                case 'seller':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'productTags':
                    $qb->addOrderBy('t.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'rating':
                    $qb->addOrderBy('r.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                default:
                    $qb->addOrderBy('a.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
            }
        } else {
            $qb->addOrderBy('u.startDate', 'desc');
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getAutomatedEndingProducts(\DateTime $endDate)
    {
        $endDate->setTime(23, 59);

        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->andWhere('a.endDate <= :endDate')
            ->setParameter('status', AuctionInterface::STATUS_BIDDING)
            ->setParameter('endDate', $endDate);

        return $qb->getQuery()->getResult();
    }
}