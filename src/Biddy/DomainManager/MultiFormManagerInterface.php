<?php

namespace Biddy\DomainManager;

interface MultiFormManagerInterface
{
    /**
     * @param $model
     * @return bool
     */
    public function getFormTypeByModel($model);
}