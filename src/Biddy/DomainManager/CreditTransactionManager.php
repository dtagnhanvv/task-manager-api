<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Repository\Core\CreditTransactionRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;

class CreditTransactionManager implements CreditTransactionManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, CreditTransactionRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, CreditTransactionInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof CreditTransactionInterface) throw new InvalidArgumentException('expect CreditTransactionInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof CreditTransactionInterface) throw new InvalidArgumentException('expect CreditTransactionInterface object');

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