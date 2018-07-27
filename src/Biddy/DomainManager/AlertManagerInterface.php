<?php

namespace Biddy\DomainManager;

interface AlertManagerInterface extends ManagerInterface
{   
    public function deleteAlertsByIds($ids);

    public function updateMarkAsReadByIds($ids);

    public function updateMarkAsUnreadByIds($ids);
}