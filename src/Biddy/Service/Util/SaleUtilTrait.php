<?php


namespace Biddy\Service\Util;

use Biddy\Bundle\UserSystem\SaleBundle\Entity\User;
use Biddy\Model\PagerParam;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

trait SaleUtilTrait
{
    private $correctFieldMapping = [
        'name' => 'firstName',
        'idd' => 'id'
    ];

    private $sortMapping = [
        'id' => 'u.id',
        'username' => 'u.username',
        'email' => 'u.email',
        'phone' => 'u.phone',
        'userType' => 'u.type',
        'lastLogin' => 'u.lastLogin',
        'status' => 'u.enabled',
        'name' => 'u.firstName'
    ];

    /**
     * @param EntityManagerInterface $em
     * @param PagerParam $param
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getUserQuery(EntityManagerInterface $em, PagerParam $param)
    {
        /** @var EntityRepository $saleRepository */
        $saleRepository = $em->getRepository(User::class);
        $qb = $saleRepository->createQueryBuilder('u');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());

            $orX = $qb->expr()->orX();
            $conditions = array(
                $qb->expr()->like('u.id', ':searchKey'),
                $qb->expr()->like('u.username', ':searchKey'),
                $qb->expr()->like('u.email', ':searchKey'),
                $qb->expr()->like('u.phone', ':searchKey'),
            );
            $orX->addMultiple($conditions);

            $qb
                ->andWhere($orX)
                ->setParameter('searchKey', $searchLike);

            $searchLike = sprintf('%%%s%%', str_replace("/", "-", $param->getSearchKey()));
            $qb
                ->orWhere($qb->expr()->like('SUBSTRING(u.lastLogin, 0, 10)', ':date'))
                ->setParameter('date', $searchLike);
        }

        $this->addFilters($qb, $param);

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            array_key_exists($param->getSortField(), $this->sortMapping)
        ) {
            switch ($param->getSortField()) {
                default:
                    $qb->addOrderBy($this->sortMapping[$param->getSortField()], $param->getSortDirection());
                    break;
            }
        } else {
            $qb->addOrderBy('u.lastLogin', 'desc');
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
        foreach ($searches as $searchField => $searchValue) {
            if (is_null($searchValue) || strlen($searchValue) < 1) {
                continue;
            }

            $searchField = array_key_exists($searchField, $this->correctFieldMapping) ? $this->correctFieldMapping[$searchField] : $searchField;

            if (in_array($searchField, ['id', 'status'])) {
                //Only `id` and `status` need search Equal/In
                if ($searchField === 'status')
                    $searchField = 'enabled';

                if (count(explode(",", $searchValue)) < 2) {
                    //Simple filter: value
                    $qb
                        ->andWhere(sprintf("u.%s = :%s", $searchField, $searchField))
                        ->setParameter(sprintf('%s', $searchField), $searchValue);
                } else {
                    //Multi filter: value1, value2, value3
                    $searchValues = explode(",", $searchValue);
                    $searchValues = array_map(function ($search) {
                        return sprintf('%s', trim($search));
                    }, $searchValues);

                    $orX = $qb->expr()->orX();
                    $conditions = array(
                        $qb->expr()->in(sprintf("u.%s", $searchField), $searchValues),
                    );
                    $orX->addMultiple($conditions);

                    $qb
                        ->andWhere($orX);
                }
            } else {
                //'username', 'email', 'phone', 'lastLogin' need search by LIKE
                $searchValue = sprintf('%%%s%%', $searchValue);

                $orX = $qb->expr()->orX();
                $conditions = array(
                    $qb->expr()->like(sprintf("u.%s", $searchField), sprintf(":%s", $searchField)),
                );
                $orX->addMultiple($conditions);

                $qb
                    ->andWhere($orX)
                    ->setParameter(sprintf('%s', $searchField), $searchValue);
            }
        }

        return $qb;
    }
}