<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\TagInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Repository\Core\TagRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;

class TagManager implements TagManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, TagRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, TagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof TagInterface) throw new InvalidArgumentException('expect TagInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof TagInterface) throw new InvalidArgumentException('expect TagInterface object');

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
    public function findTagsByProduct(ProductInterface $product, $page, $limit)
    {
        return $this->repository->findTagsByProduct($product, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findTagsByTag(TagInterface $tag, $page = 1, $limit = 10)
    {
        return $this->repository->findTagsByTag($tag, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findTotalTagsCountByProduct(ProductInterface $product)
    {
        return $this->repository->findTotalTagsCountByProduct($product);
    }

    /**
     * @inheritdoc
     */
    public function findTotalTagsCountByTag(TagInterface $tag)
    {
        return $this->repository->findTotalTagsCountByTag($tag);
    }
}