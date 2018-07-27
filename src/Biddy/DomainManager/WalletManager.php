<?php

namespace Biddy\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\ModelInterface;
use Biddy\Repository\Core\WalletRepositoryInterface;

class WalletManager implements WalletManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, WalletRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, WalletInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $wallet)
    {
        if (!$wallet instanceof WalletInterface) throw new InvalidArgumentException('expect WalletInterface object');

        $this->om->persist($wallet);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $wallet)
    {
        if (!$wallet instanceof WalletInterface) throw new InvalidArgumentException('expect WalletInterface object');

        $this->om->remove($wallet);
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