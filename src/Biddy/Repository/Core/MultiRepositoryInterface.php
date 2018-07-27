<?php

namespace Biddy\Repository\Core;


interface MultiRepositoryInterface
{
    /**
     * @param $type
     * @return mixed
     */
    public function supportsEntity($type);
}