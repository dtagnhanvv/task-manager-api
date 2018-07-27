<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Repository\Core\CommentRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;

class CommentManager implements CommentManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, CommentRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, CommentInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof CommentInterface) throw new InvalidArgumentException('expect CommentInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof CommentInterface) throw new InvalidArgumentException('expect CommentInterface object');

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
    public function findCommentsByProduct($user, ProductInterface $product, $page, $limit)
    {
        return $this->repository->findCommentsByProduct($user, $product, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findCommentsByComment(CommentInterface $comment, $page = 1, $limit = 10)
    {
        return $this->repository->findCommentsByComment($comment, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findTotalCommentsCountByProduct(ProductInterface $product)
    {
        return $this->repository->findTotalCommentsCountByProduct($product);
    }

    /**
     * @inheritdoc
     */
    public function findTotalCommentsCountByComment(CommentInterface $comment)
    {
        return $this->repository->findTotalCommentsCountByComment($comment);
    }
}