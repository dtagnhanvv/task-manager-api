<?php

namespace Biddy\EventListener\Alert\Credit;

use Biddy\Model\User\Role\AccountInterface;
use Biddy\Service\Alert\ProcessAlertInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CreateAlertForAccountCreditChangeListener
{
    private $updateEntities = [];
    private $changeFields = ['credit'];

    /** @var Manager */
    protected $workerManager;

    /**
     * CreateAlertForAccountCreditChangeListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AccountInterface) {
            return;
        }

        if (count(array_intersect(array_keys($args->getEntityChangeSet()), $this->changeFields)) < 1) {
            return;
        }

        $this->updateEntities[$entity->getId()] = $entity;
    }

    /**
     * @param PostFlushEventArgs $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->createAlertForUpdateEntities();
    }

    private function createAlertForUpdateEntities()
    {
        $updateEntities = $this->updateEntities;
        $this->updateEntities = [];

        $ids = array_map(function ($item) {
            if ($item instanceof AccountInterface) {
                return $item->getId();
            }
        }, $updateEntities);

        if (!empty($ids)) {
            $this->workerManager->processAlert(AccountInterface::class, $ids, ProcessAlertInterface::ACTION_UPDATE, CreateAlertForAccountCreditChangeListener::class);
        }
    }
}