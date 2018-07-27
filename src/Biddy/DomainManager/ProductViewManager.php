<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\ProductViewInterface;
use Biddy\Repository\Core\ProductViewRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;

class ProductViewManager implements ProductViewManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, ProductViewRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, ProductViewInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof ProductViewInterface) throw new InvalidArgumentException('expect ProductViewInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof ProductViewInterface) throw new InvalidArgumentException('expect ProductViewInterface object');

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
}