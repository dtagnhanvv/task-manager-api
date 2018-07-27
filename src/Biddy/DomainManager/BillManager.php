<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\BillInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Repository\Core\BillRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;

class BillManager implements BillManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, BillRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, BillInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof BillInterface) throw new InvalidArgumentException('expect BillInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof BillInterface) throw new InvalidArgumentException('expect BillInterface object');

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
    public function getBillsForBuyer(AccountInterface $buyer, $status = BillInterface::STATUS_UNCONFIRMED, $page = 1, $limit = 10)
    {
        // TODO: Implement getBillsForBuyer() method.
    }
}