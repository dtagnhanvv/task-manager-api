<?php


namespace Biddy\Service\Util;

use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Repository\Core\BidRepositoryInterface;

trait AccountUtilTrait
{
    /**
     * @param $records
     * @param AuctionInterface $auction
     * @param BidRepositoryInterface $bidRepository
     * @param AccountManagerInterface $accountManager
     * @return mixed
     */
    public function serializeBuyers($records, AuctionInterface $auction, BidRepositoryInterface $bidRepository, AccountManagerInterface $accountManager)
    {
        $groups = [];
        foreach ($records as $record) {
            if (!isset($record['id'])) {
                continue;
            }
            $account = $accountManager->find($record['id']);
            if (!$account instanceof UserRoleInterface) {
                continue;
            }

            $group['buyer'] = $this->serializeSingleAccount($account);
            if ($auction->getObjective() == AuctionInterface::OBJECTIVE_HIGHEST_PRICE) {
                $bid = $bidRepository->findBids($auction, $account, 'desc');
            } else {
                $bid = $bidRepository->findBids($auction, $account, 'asc');
            }

            if ($bid instanceof BidInterface) {
                $group['price'] = $bid->getPrice();
                $group['id'] = $bid->getId();
                $group['createdDate'] = $bid->getCreatedDate();
            }

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * @param UserRoleInterface $user
     * @return mixed
     */
    public function serializeSingleAccount(UserRoleInterface $user)
    {
        $group['id'] = $user->getId();
        $group['profileImageUrl'] = $user->getProfileImageUrl();
        $group['username'] = $user->getUsername();
        $group['rating'] = $user->getRating();

        return $group;
    }

    /**
     * @param AccountInterface $user
     * @param AccountManagerInterface $accountManager
     * @param $messages
     * @throws PublicSimpleException
     */
    private function checkDuplicateUserInfo(AccountInterface $user, AccountManagerInterface $accountManager, $messages)
    {
        $currentUser = $accountManager->findUserByUsernameOrEmail($user->getEmail());
        if ($currentUser instanceof AccountInterface && $currentUser->getId() != $user->getId()) {
            throw new PublicSimpleException($messages['duplicate_email']);
        }

        $currentUser = $accountManager->findUserByUsernameOrEmail($user->getUsername());
        if ($currentUser instanceof AccountInterface && $currentUser->getId() != $user->getId()) {
            throw new PublicSimpleException($messages['duplicate_username']);
        }

        $currentUser = $accountManager->findUserByPhone($user->getPhone());
        if ($currentUser instanceof AccountInterface && $currentUser->getId() != $user->getId()) {
            throw new PublicSimpleException($messages['duplicate_phone']);
        }
    }
}