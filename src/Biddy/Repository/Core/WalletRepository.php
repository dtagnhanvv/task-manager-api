<?php

namespace Biddy\Repository\Core;

use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\ORM\EntityRepository;

class WalletRepository extends EntityRepository implements WalletRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'createdDate' => 'createdDate', 'title' => 'code', 'type' => 'type',
        'targetType' => 'targetType', 'targetId' => 'targetId'
    ];

    public function getWalletsForUserQuery(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = [
                $qb->expr()->like('a.id', ':searchKey'),
                $qb->expr()->like('a.code', ':searchKey'),
                $qb->expr()->like('a.type', ':searchKey'),
                $qb->expr()->like('a.createdDate', ':searchKey')
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
                case 'id':
                    $qb->addOrderBy('a.' . $param->getSortField(), $param->getSortDirection());
                    break;

                case 'createdDate':
                    $qb->addOrderBy('a.' . $param->getSortField(), $param->getSortDirection());
                    break;

                case 'title':
                    $qb->addOrderBy('a.' . $this->SORT_FIELDS['title'], $param->getSortDirection());
                    break;

                case 'type':
                    $qb->addOrderBy('a.' . $this->SORT_FIELDS['type'], $param->getSortDirection());
                    break;

                default:
                    break;
            }
        } else {
            $qb->addOrderBy('a.createdDate', 'desc');
        }

        return $qb;
    }

    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.owner', 'p')
            ->select('a, p')
            ->where('a.owner = :owner')
            ->setParameter('owner', $user);

        return $qb;
    }

    public function getWalletsForAccountQuery(AccountInterface $owner, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.owner', 'p')
            ->select('a, p')
            ->where('a.owner = :owner')
            ->setParameter('owner', $owner);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }
}