<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface CommentInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $id
     * @return self
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate);

    /**
     * @return mixed
     */
    public function getModified();

    /**
     * @param mixed $modified
     */
    public function setModified($modified);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @param mixed $deletedAt
     */
    public function setDeletedAt($deletedAt);

    /**
     * @return mixed
     */
    public function getContent();

    /**
     * @param mixed $content
     */
    public function setContent($content);

    /**
     * @return mixed
     */
    public function getContentType();

    /**
     * @param mixed $contentType
     */
    public function setContentType($contentType);

    /**
     * @return mixed
     */
    public function getEstimatedHeight();

    /**
     * @param mixed $estimatedHeight
     */
    public function setEstimatedHeight($estimatedHeight);

    /**
     * @return mixed
     */
    public function getMigrationStatus();

    /**
     * @param mixed $migrationStatus
     */
    public function setMigrationStatus($migrationStatus);

    /**
     * @return mixed
     */
    public function getRaw();

    /**
     * @param mixed $raw
     */
    public function setRaw($raw);

    /**
     * @return AccountInterface
     */
    public function getAuthor();

    /**
     * @param AccountInterface $author
     */
    public function setAuthor($author);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     */
    public function setProduct($product);

    /**
     * @return CommentInterface
     */
    public function getMasterComment();

    /**
     * @param CommentInterface $masterComment
     * @return self
     */
    public function setMasterComment($masterComment);

    /**
     * @return CommentInterface[]
     */
    public function getChildComments();

    /**
     * @param CommentInterface[] $childComments
     */
    public function setChildComments($childComments);

    /**
     * @return ReactionInterface[]
     */
    public function getReactions();

    /**
     * @param ReactionInterface[] $reactions
     * @return self
     */
    public function setReactions($reactions);
}