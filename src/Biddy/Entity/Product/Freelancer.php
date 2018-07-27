<?php

namespace Biddy\Entity\Product;


use Biddy\Entity\Core\Product;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Product\FreelancerInterface;

class Freelancer extends Product implements FreelancerInterface
{
    protected $requirements;
    protected $gender;
    protected $ages;

    /**
     * @inheritdoc
     *
     * inherit constructor for inheriting all default initialized value
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return ProductInterface::TYPE_FREELANCER;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @inheritdoc
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAges()
    {
        return $this->ages;
    }

    /**
     * @inheritdoc
     */
    public function setAges($ages)
    {
        $this->ages = $ages;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @inheritdoc
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

}