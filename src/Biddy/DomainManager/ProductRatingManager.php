<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\ProductRatingInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Repository\Core\ProductRatingRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;

class ProductRatingManager implements ProductRatingManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, ProductRatingRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, ProductRatingInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof ProductRatingInterface) throw new InvalidArgumentException('expect ProductRatingInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof ProductRatingInterface) throw new InvalidArgumentException('expect ProductRatingInterface object');

        $this->om->remove($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew($type = null)
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentProductRatingsForBuyer(AccountInterface $buyer, $page = 1, $limit = 10)
    {
        // TODO: Implement getCurrentProductRatingsForBuyer() method.
    }

    /**
     * @param AccountInterface $seller
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getProductRatingsForBuyer(AccountInterface $seller, $page = 1, $limit = 10)
    {
        return $this->repository->getProductRatingsBySellers($seller, $page, $limit);
    }
}