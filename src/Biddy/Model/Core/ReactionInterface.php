<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\Role\AccountInterface;

interface ReactionInterface extends ModelInterface
{
    const HAPPY ='happy';
    const LOVE = 'love';
    const ANGRY = 'angry';
    const HAHA = 'haha';
    const WOW = 'wow';

    const SUPPORT_REACTIONS = [
        self::HAPPY,
        self::LOVE,
        self::HAHA,
        self::ANGRY,
        self::WOW
    ];

    /**
     * @return mixed
     */
    public function getEmotion();

    /**
     * @param mixed $emotion
     * @return self
     */
    public function setEmotion($emotion);


    /**
     * @return AccountInterface
     */
    public function getViewer();

    /**
     * @param AccountInterface $viewer
     * @return self
     */
    public function setViewer($viewer);

    /**
     * @param $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     * @return self
     */
    public function setCreatedDate($createdDate);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     * @return self
     */
    public function setProduct($product);

    /**
     * @return CommentInterface
     */
    public function getComment();

    /**
     * @param CommentInterface $comment
     * @return self
     */
    public function setComment($comment);
}