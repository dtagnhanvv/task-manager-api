<?php

namespace Biddy\Model\Product;

use Biddy\Model\Core\ProductInterface;

interface FreelancerInterface extends ProductInterface
{
    /**
     * @return mixed
     */
    public function getRequirements();

    /**
     * @param mixed $requires
     * @return self
     */
    public function setRequirements($requires);

    /**
     * @return mixed
     */
    public function getGender();

    /**
     * @param mixed $gender
     * @return self
     */
    public function setGender($gender);

    /**
     * @return mixed
     */
    public function getAges();

    /**
     * @param mixed $ages
     * @return self
     */
    public function setAges($ages);
}