<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class BidRepository extends EntityRepository implements BidRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'createdDate' => 'createdDate', 'price' => 'price', 'category' => 'category', 'quantity' => 'quantity', 'product' => 'subject', 'buyer' => 'username'];

    public function getBidsForUserQuery(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.price', ':searchKey'),
                $qb->expr()->like('a.category', ':searchKey'),
                $qb->expr()->like('a.quantity', ':searchKey')
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
                case 'product':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'buyer':
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
     * @inheritdoc
     */
    public function getBidsForProductQuery(ProductInterface $product, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.auction', 'u')
            ->where('u.product = :product')
            ->setParameter('product', $product);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.price', ':searchKey'),
                $qb->expr()->like('a.category', ':searchKey'),
                $qb->expr()->like('a.quantity', ':searchKey')
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
                case 'product':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'buyer':
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
     * @inheritdoc
     */
    public function getBidsForAuctionQuery(AuctionInterface $auction, PagerParam $param, UserRoleInterface $user)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.auction = :auction')
            ->setParameter('auction', $auction);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.price', ':searchKey'),
                $qb->expr()->like('a.category', ':searchKey'),
                $qb->expr()->like('a.quantity', ':searchKey')
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

        if (!$auction->isShowBid() && $user instanceof AccountInterface && $auction->getProduct()->getSeller()->getId() != $user->getId()) {
            $qb
                ->andWhere('a.buyer = :buyer')
                ->setParameter('buyer', $user);
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
                case 'buyer':
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

    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof AccountInterface ? $this->getBidsForAccountQuery($user) :
            $this->createQueryBuilder('a')
                ->leftJoin('a.buyer', 'b')
                ->leftJoin('a.product', 'p');
    }

    public function getBidsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.buyer', 'p')
            ->select('a, p')
            ->where('a.buyer = :account')
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
     * @inheritdoc
     */
    public function getUserBidsForProductQuery(AuctionInterface $auction, AccountInterface $account)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.buyer = :account')
            ->andWhere('a.auction = :auction')
            ->setParameter('account', $account)
            ->setParameter('auction', $auction);

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getTotalBidsForAuction(AuctionInterface $auction)
    {
        $statuses = implode("','", BidInterface::COUNT_STATUS);

        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.auction = :auction')
            ->andWhere(sprintf("a.status in ('%s')", $statuses))
            ->setParameter('auction', $auction);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function getHighestPriceForAuction(AuctionInterface $auction)
    {
        $statuses = implode("','", BidInterface::COUNT_STATUS);

        $qb = $this->createQueryBuilder('a')
            ->select('a.price')
            ->andWhere('a.auction = :auction')
            ->andWhere(sprintf("a.status in ('%s')", $statuses))
            ->setParameter('auction', $auction)
            ->orderBy('a.price', 'desc');

        try {
            $qb->setFirstResult(0);
            $qb->setMaxResults(1);
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }

    }

    /**
     * @inheritdoc
     */
    public function getLowestPriceForAuction(AuctionInterface $auction)
    {
        $statuses = implode("','", BidInterface::COUNT_STATUS);

        $qb = $this->createQueryBuilder('a')
            ->select('a.price')
            ->andWhere('a.auction = :auction')
            ->andWhere(sprintf("a.status in ('%s')", $statuses))
            ->setParameter('auction', $auction)
            ->orderBy('a.price', 'asc');

        try {
            $qb->setFirstResult(0);
            $qb->setMaxResults(1);
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @inheritdoc
     */
    public function getTotalBuyersForAuction(AuctionInterface $auction)
    {
        $statuses = implode("','", BidInterface::COUNT_STATUS);

        $qb = $this->createQueryBuilder('a')
            ->select('count(DISTINCT a.buyer)')
            ->andWhere('a.auction = :auction')
            ->andWhere(sprintf("a.status in ('%s')", $statuses))
            ->setParameter('auction', $auction);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function getOnTopProduct(AuctionInterface $auction, UserRoleInterface $user)
    {
        $highestPrice = $this->getHighestPriceForAuction($auction);
        $qb = $this->createQueryBuilder('a')
            ->where('a.buyer = :account')
            ->andWhere('a.auction = :auction')
            ->andWhere('a.price = :price')
            ->setParameter('account', $user)
            ->setParameter('auction', $auction)
            ->setParameter('price', $highestPrice);

        $result = $qb->getQuery()->getResult();

        if (empty($result)) {
            return 'notOnTop';
        }

        return 'onTop';
    }

    /**
     * @inheritdoc
     */
    public function getUserBiddingForProductQuery(AuctionInterface $auction, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('a')
            ->distinct(true)
            ->leftJoin('a.buyer', 'b')
            ->leftJoin('a.auction', 'u')
            ->leftJoin('u.product', 'p')
            ->select('b.id')
            ->andWhere('a.auction = :auction')
            ->andWhere('a.status = :status')
            ->setParameter('auction', $auction)
            ->setParameter('status', BidInterface::STATUS_BIDDING);

        if (is_string($param->getSearchKey()) && !empty($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.price', ':searchKey'),
                $qb->expr()->like('a.category', ':searchKey'),
                $qb->expr()->like('a.quantity', ':searchKey'),
                $qb->expr()->like('b.username', ':searchKey')
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
                case 'product':
                    $qb->addOrderBy('p.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                case 'buyer':
                    $qb->addOrderBy('b.' . $this->SORT_FIELDS[$param->getSortField()], $param->getSortDirection());
                    break;
                default:
                    $qb->addOrderBy('a.' . $param->getSortField(), $param->getSortDirection());
                    break;
            }
        } else {
            $qb->addOrderBy('a.price', 'desc');
        }

        return [$qb, count($qb->getQuery()->getResult())];
    }

    /**
     * @inheritdoc
     */
    public function findBids(AuctionInterface $auction, UserRoleInterface $account, $sortDirection = 'desc')
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.buyer = :account')
            ->andWhere('a.auction = :auction')
            ->andWhere('a.status = :status')
            ->setParameter('account', $account)
            ->setParameter('auction', $auction)
            ->setParameter('status', BidInterface::STATUS_BIDDING)
            ->orderBy('a.price', $sortDirection);

        $bids = $qb->getQuery()->getResult();

        return reset($bids);
    }

    /**
     * @inheritdoc
     */
    public function getHighestPriceBid(AuctionInterface $auction)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.auction = :auction')
            ->andWhere('a.status = :status')
            ->andWhere('a.price >= :price')
            ->setParameter('auction', $auction)
            ->setParameter('price', $auction->getMinimumPrice())
            ->setParameter('status', BidInterface::STATUS_BIDDING)
            ->orderBy('a.price', 'desc');

        try {
            $qb->setFirstResult(0);
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getResult();

            return reset($result);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getLowestPriceBid(AuctionInterface $auction)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.auction = :auction')
            ->andWhere('a.status = :status')
            ->setParameter('auction', $auction)
            ->setParameter('status', BidInterface::STATUS_BIDDING)
            ->orderBy('a.price', 'asc');

        try {
            $qb->setFirstResult(0);
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getResult();

            return reset($result);
        } catch (\Exception $e) {
            return null;
        }
    }
}
