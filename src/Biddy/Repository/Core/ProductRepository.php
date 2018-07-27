<?php

namespace Biddy\Repository\Core;

use Biddy\Model\Core\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Biddy\Model\PagerParam;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\UserRoleInterface;
use Doctrine\ORM\QueryBuilder;

class ProductRepository extends EntityRepository implements ProductRepositoryInterface
{
    const CORRECT_FIELD_NAMES = ['idd' => 'id'];
    protected $SORT_FIELDS = ['id' => 'id', 'address' => 'address', 'createdDate' => 'createdDate', 'mode' => 'mode', 'subject' => 'subject', 'visibility' => 'visibility',
        'businessSetting' => 'businessSetting', 'businessRule' => 'businessRule', 'commentVisibility' => 'commentVisibility', 'seller' => 'username',
        'productTags' => 'name',
        'rating' => 'rating'
    ];

    const FIND_EXACTLY_FIELDS = ['businessRule', 'businessSetting', 'mode', 'visibility'];
    const CORRECT_FIELDS_MAPPING = [
        'productTags' => 't.name',
        'rating' => 'r.rating',
        'seller' => 'p.username'
    ];

    /**
     * @inheritdoc
     */
    public function supportsEntity($type) {
        return $type == ProductInterface::TYPE_PRODUCT || $type instanceof ProductInterface;
    }

    public function getProductsForUserQuery($user, PagerParam $param)
    {
//        $qb = $this->createQueryBuilderForUser($user);
        $qb = $this->createQueryBuilderForAllUser();

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
                $qb->expr()->like('a.rating', ':searchKey'),
                $qb->expr()->like('t.name', ':searchKey'),
                $qb->expr()->like('p.username', ':searchKey'),
                $qb->expr()->like('p.phone', ':searchKey'),
                $qb->expr()->like('p.firstName', ':searchKey'),
                $qb->expr()->like('p.city', ':searchKey'),
                $qb->expr()->like('p.address', ':searchKey'),
                $qb->expr()->like('p.emailSendAlert', ':searchKey'),
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

        $qb = $this->addFilters($qb, $param, $user);

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
    public function getProductsForUserBiddingQuery(UserRoleInterface $account, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('a')
            ->distinct(true)
            ->leftJoin('a.auctions', 'u')
            ->leftJoin('u.bids', 'b')
            ->leftJoin('a.seller', 'p')
            ->leftJoin('a.productTags', 'pt')
            ->leftJoin('a.productRatings', 'r')
            ->leftJoin('pt.tag', 't')
            ->where('b.buyer = :buyer')
            ->setParameter('buyer', $account);

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

    private function createQueryBuilderForAllUser()
    {
        return $this->createQueryBuilder('a')
            ->distinct(true)
            ->leftJoin('a.seller', 'p')
            ->leftJoin('a.productTags', 'pt')
            ->leftJoin('a.productRatings', 'r')
            ->leftJoin('pt.tag', 't')
            ->distinct(true);
    }

    /**
     * @param AccountInterface $account
     * @param null $limit
     * @param null $offset
     * @return QueryBuilder
     */
    public function getAuctionsForAccountQuery(AccountInterface $account, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->distinct(true)
            ->leftJoin('a.seller', 'p')
            ->leftJoin('a.productTags', 'pt')
            ->leftJoin('pt.tag', 't')
            ->select('a, p')
            ->where('a.seller = :account')
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
        if (empty($searches)) {
            $searches['visibility'] = implode(",", ProductInterface::SUPPORT_COMMENT_VISIBILITIES);
            $searches['mode'] = implode(",", ProductInterface::SUPPORT_MODES);
        }

        foreach ($searches as $searchField => $searchLike) {
            if (is_null($searchLike) || strlen($searchLike) < 1) {
                continue;
            }

            $searchField = array_key_exists($searchField, self::CORRECT_FIELD_NAMES) ? self::CORRECT_FIELD_NAMES[$searchField] : $searchField;

            if ($searchField == 'visibility') {
                $qb = $this->addFilterForVisibility($qb, $searchLike, $user);
                continue;
            }

            if ($searchField == 'mode') {
                $qb = $this->addFilterForMode($qb, $searchLike, $user);
                continue;
            }

            if ($searchField == 'rating') {
                $qb = $this->addFilterForRating($qb, $searchLike);
                continue;
            }

            if (in_array($searchField, self::FIND_EXACTLY_FIELDS)) {
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
                    $conditions = [
                        $qb->expr()->in(sprintf("a.%s", $searchField), $searchLikes),
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
                        $qb->expr()->like(sprintf("a.%s", $searchField), sprintf(":%s", $searchField)),
                    ];
                }

                $orX->addMultiple($conditions);

                $qb
                    ->andWhere($orX)
                    ->setParameter(sprintf('%s', $searchField), $searchLike);
            }
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $visibilities
     * @param $user
     * @return QueryBuilder
     */
    private function addFilterForVisibility(QueryBuilder $qb, $visibilities, $user)
    {
        $visibilities = explode(",", $visibilities);

        $orX = $qb->expr()->orX();
        $conditions = [];
        foreach ($visibilities as $visibility) {
            if ($visibility == ProductInterface::VISIBILITY_PRIVATE) {
                if ($user instanceof AccountInterface) {
                    $andX = $qb->expr()->andX();
                    $conditionPrivates[] = $qb->expr()->like(sprintf("a.%s", 'visibility'), sprintf("'%s'", $visibility));
                    $conditionPrivates[] = $qb->expr()->eq(sprintf("a.%s", 'seller'), sprintf("'%s'", $user->getId()));
                    $andX->addMultiple($conditionPrivates);
                    $conditions[] = $andX;
                } else {
                    $andX = $qb->expr()->andX();
                    $conditionPrivates[] = $qb->expr()->like(sprintf("a.%s", 'visibility'), sprintf("'%s'", $visibility));
                    $andX->addMultiple($conditionPrivates);
                    $conditions[] = $andX;
                }

                continue;
            }

            $conditions[] = $qb->expr()->like(sprintf("a.%s", 'visibility'), sprintf("'%s'", $visibility));
        }
        $orX->addMultiple($conditions);
        $qb->andWhere($orX);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $modes
     * @param $user
     * @return QueryBuilder
     */
    private function addFilterForMode(QueryBuilder $qb, $modes, $user)
    {
        $modes = explode(",", $modes);

        $orX = $qb->expr()->orX();
        $conditions = [];
        foreach ($modes as $mode) {
            if ($mode == ProductInterface::MODE_DRAFT) {
                if ($user instanceof AccountInterface) {
                    $andX = $qb->expr()->andX();
                    $conditionPrivates[] = $qb->expr()->like(sprintf("a.%s", 'mode'), sprintf("'%s'", $mode));
                    $conditionPrivates[] = $qb->expr()->eq(sprintf("a.%s", 'seller'), sprintf("'%s'", $user->getId()));
                    $andX->addMultiple($conditionPrivates);
                    $conditions[] = $andX;
                } else {
                    $andX = $qb->expr()->andX();
                    $conditionPrivates[] = $qb->expr()->like(sprintf("a.%s", 'mode'), sprintf("'%s'", $mode));
                    $andX->addMultiple($conditionPrivates);
                    $conditions[] = $andX;
                }

                continue;
            }

            $conditions[] = $qb->expr()->like(sprintf("a.%s", 'mode'), sprintf("'%s'", $mode));
        }
        $orX->addMultiple($conditions);
        $qb->andWhere($orX);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $rate
     * @return QueryBuilder
     */
    private function addFilterForRating(QueryBuilder $qb, $rate)
    {
        $qb
            ->andWhere('a.rating = :rating')
            ->setParameter(sprintf('%s', 'rating'), floatval($rate));

        return $qb;
    }
}
