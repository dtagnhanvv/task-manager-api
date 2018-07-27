<?php

namespace Biddy\Model\Product;

use Biddy\Model\Core\ProductInterface;

interface ProfessionalInterface extends ProductInterface
{
    /**
     * @inheritdoc
     */
    public function getRequirements();

    /**
     * @inheritdoc
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

    /**
     * @return mixed
     */
    public function getSkills();

    /**
     * @param mixed $skills
     * @return self
     */
    public function setSkills($skills);
}