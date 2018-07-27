<?php

namespace Biddy\Worker\Core;

use Biddy\Worker\Core\Exception\MissingJobParamException;

class JobParams
{
    /**
     * @var array
     */
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function getRequiredParam(string $param)
    {
        if (!array_key_exists($param, $this->params)) {
            throw new MissingJobParamException(sprintf('Required param "%s" is missing', $param));
        }

        return $this->params[$param];
    }

    public function getParam(string $param, $defaultValue = null)
    {
        if (!array_key_exists($param, $this->params)) {
            return $defaultValue;
        }

        return $this->params[$param];
    }
}