<?php

namespace Biddy\DomainManager;

use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\Core\TaskInterface;
use Biddy\Model\ModelInterface;
use Biddy\Repository\Core\TaskRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;

class TaskManager implements TaskManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, TaskRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, TaskInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof TaskInterface) throw new InvalidArgumentException('expect TaskInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof TaskInterface) throw new InvalidArgumentException('expect TaskInterface object');

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