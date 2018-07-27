<?php

namespace Biddy\DomainManager;

use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ReactionInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Repository\Core\ReactionRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;

class ReactionManager implements ReactionManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, ReactionRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, ReactionInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof ReactionInterface) throw new InvalidArgumentException('expect ReactionInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof ReactionInterface) throw new InvalidArgumentException('expect ReactionInterface object');

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
    public function findReactionsByProduct(ProductInterface $product, $page, $limit)
    {
        return $this->repository->findReactionsByProduct($product, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findReactionsByComment(CommentInterface $comment, $page, $limit)
    {
        return $this->repository->findReactionsByComment($comment, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByComment(CommentInterface $comment, $page = 1, $limit = 1)
    {
        return $this->repository->findTotalReactionCountByComment($comment, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findReactionEmotionsByComment(CommentInterface $comment, $page = 1, $limit = 1)
    {
        return $this->repository->findReactionEmotionsByComment($comment, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByProduct(ProductInterface $product, $page = 1, $limit = 1)
    {
        return $this->repository->findTotalReactionCountByProduct($product, $page, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByProductGroupByEmotion(ProductInterface $product)
    {
        return $this->repository->findTotalReactionCountByProductGroupByEmotion($product);
    }

    /**
     * @inheritdoc
     */
    public function findTotalReactionCountByCommentGroupByEmotion(CommentInterface $comment)
    {
        return $this->repository->findTotalReactionCountByCommentGroupByEmotion($comment);
    }

    /**
     * @inheritdoc
     */
    public function findReactionByUserAndObject(AccountInterface $user, $type, $object)
    {
        return $this->repository->findReactionByUserAndObject($user, $type, $object);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentReactionByUser($user, $type, ModelInterface $object)
    {
        if (!$user instanceof AccountInterface) {
            return null;
        }

        $oldReaction = $this->findReactionByUserAndObject($user, $type, $object);

        if (!$oldReaction instanceof ReactionInterface) {
            return null;
        }

        return $oldReaction->getEmotion();
    }
}