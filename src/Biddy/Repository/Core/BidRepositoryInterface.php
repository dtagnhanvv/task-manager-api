<?php

namespace Biddy\Repository\Core;


use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\Role\AccountInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\UserRoleInterface;

interface BidRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $params
     * @return QueryBuilder
     */
    public function getBidsForUserQuery(UserRoleInterface $user, PagerParam $params);

    /**
     * @param ProductInterface $product
     * @param PagerParam $param
     * @return QueryBuilder
     */
    public function getBidsForProductQuery(ProductInterface $product, PagerParam $param);

    /**
     * @param AuctionInterface $auction
     * @param PagerParam $param
     * @param UserRoleInterface $userRole
     * @return QueryBuilder
     */
    public function getBidsForAuctionQuery(AuctionInterface $auction, PagerParam $param, UserRoleInterface $userRole);

    /**
     * @param AuctionInterface $auction
     * @param AccountInterface $account
     * @return mixed
     */
    public function getUserBidsForProductQuery(AuctionInterface $auction, AccountInterface $account);

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function getTotalBidsForAuction(AuctionInterface $auction);

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function getHighestPriceForAuction(AuctionInterface $auction);

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function getLowestPriceForAuction(AuctionInterface $auction);

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function getTotalBuyersForAuction(AuctionInterface $auction);

    /**
     * @param AuctionInterface $auction
     * @param UserRoleInterface $user
     * @return mixed
     */
    public function getOnTopProduct(AuctionInterface $auction, UserRoleInterface $user);

    /**
     * @param AuctionInterface $auction
     * @param PagerParam $param
     * @return mixed
     */
    public function getUserBiddingForProductQuery(AuctionInterface $auction, PagerParam $param);

    /**
     * @param AuctionInterface $auction
     * @param UserRoleInterface $account
     * @param string $sortDirection
     * @return mixed
     */
    public function findBids(AuctionInterface $auction, UserRoleInterface $account, $sortDirection = 'desc');

    /**
     * @param AuctionInterface $auction
     * @return mixed
     */
    public function getHighestPriceBid(AuctionInterface $auction);

    /**
     * @param AuctionInterface $auction
     * @return BidInterface
     */
    public function getLowestPriceBid(AuctionInterface $auction);
}