<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;
use Biddy\Model\User\UserEntityInterface;

interface TaskInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @param mixed $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt);

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
     * @return mixed
     */
    public function getCardNumber();

    /**
     * @param mixed $cardNumber
     * @return self
     */
    public function setCardNumber($cardNumber);

    /**
     * @return mixed
     */
    public function getReleasePlan();

    /**
     * @param mixed $releasePlan
     * @return self
     */
    public function setReleasePlan($releasePlan);

    /**
     * @return mixed
     */
    public function getBoard();

    /**
     * @param mixed $board
     * @return self
     */
    public function setBoard($board);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @param mixed $status
     * @return self
     */
    public function setStatus($status);

    /**
     * @return mixed
     */
    public function getReview();

    /**
     * @param mixed $review
     * @return self
     */
    public function setReview($review);

    /**
     * @return UserEntityInterface
     */
    public function getOwner();

    /**
     * @param UserEntityInterface $owner
     * @return self
     */
    public function setOwner($owner);

    /**
     * @return UserEntityInterface
     */
    public function getReviewer();

    /**
     * @param UserEntityInterface $reviewer
     * @return self
     */
    public function setReviewer($reviewer);
}