<?php

namespace Biddy\Service\Alert;


interface ProcessAlertInterface
{
    const ACTION_CREATE = 'create_new';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'remove';

    /**
     * @param $objectType
     * @param $objectIds
     * @param $action
     * @param $context
     * @return mixed
     */
    public function createAlerts($objectType, $objectIds, $action, $context);
}