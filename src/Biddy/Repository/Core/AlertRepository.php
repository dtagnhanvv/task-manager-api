<?php

namespace Biddy\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;

class AlertRepository extends EntityRepository implements AlertRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'createdDate' => 'createdDate', 'title' => 'code', 'type' => 'type',
        'targetType' => 'targetType', 'targetId' => 'targetId'
    ];

    public function getAlertsForUserQuery(UserRoleInterface $user, PagerParam $param)
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
            $qb->addOrderBy('a.isRead', 'asc');
            $qb->addOrderBy('a.createdDate', 'desc');
        }

        return $qb;
    }

    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.account', 'p')
            ->select('a, p')
            ->where('a.account = :account')
            ->setParameter('account', $user);

        return $qb;
    }

    public function getAlertsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.account', 'p')
            ->select('a, p')
            ->where('a.account = :account')
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
    public function getAccountAlertsQuery(UserRoleInterface $user, PagerParam $param)
    {
        if ($user instanceof AccountInterface) {
            $qb = $this->createQueryBuilder('a')
                ->leftJoin('a.account', 'p')
                ->select('a, p')
                ->where('a.account = :account')
                ->setParameter('account', $user);
        } else {
            $qb = $this->createQueryBuilder('a');
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('ds.name', ':searchKey'),
                    $qb->expr()->like('a.id', ':searchKey'),
                    $qb->expr()->like('a.code', ':searchKey'),
                    $qb->expr()->like('a.type', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
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

    public function deleteAlertsByIds($ids = null)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->delete();

        if (!empty($ids)) {
            $qb->where($qb->expr()->in('a.id', $ids));
        }

        return $qb->getQuery()->getResult();
    }

    public function updateMarkAsReadByIds($ids = null)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->update()
            ->set('a.isRead', 1);

        if (!empty($ids)) {
            $qb->where($qb->expr()->in('a.id', $ids));
        }

        return $qb->getQuery()->getResult();
    }

    public function updateMarkAsUnreadByIds($ids = null)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->update()
            ->set('a.isRead', 0);

        if (!empty($ids)) {
            $qb->where($qb->expr()->in('a.id', $ids));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAlertsToSendEmailByTypesQuery(AccountInterface $account, array $types)
    {
        $qb = $this->createQueryBuilderForUser($account)
            ->andWhere('a.isSent = :sent')
            ->setParameter('sent', false);

        // support filter by alert types
        if (!empty($types)) {
            $qb
                ->andWhere('a.type IN (:types)')
                ->setParameter('types', $types);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getTotalUnread(UserRoleInterface $user)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select('count(a.id)')
            ->andWhere('a.account = :account')
            ->andWhere('a.isRead = 0')
            ->setParameter('account', $user);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function readAll(UserRoleInterface $user)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->update()
            ->set('a.isRead', 1);

        if (!empty($ids)) {
            $qb->where($qb->expr()->eq('a.isRead', 0));
        }

        return $qb->getQuery()->getResult();
    }
}