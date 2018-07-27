<?php

namespace Biddy\Form\Type;


interface MultiFormInterface
{
    /**
     * @param $type
     * @return mixed
     */
    public function supportsEntity($type);
}