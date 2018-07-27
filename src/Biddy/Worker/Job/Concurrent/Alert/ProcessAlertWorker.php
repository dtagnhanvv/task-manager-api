<?php

namespace Biddy\Worker\Job\Concurrent\Alert;

use Biddy\Worker\Core\Job\JobInterface;
use Biddy\Worker\Core\JobParams;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Monolog\Logger;
use Biddy\Service\Alert\ProcessAlertInterface;

class ProcessAlertWorker implements JobInterface
{
    const JOB_NAME = 'ProcessAlertWorker';

    const PARAM_KEY_OBJECT_TYPE = 'object_type';
    const PARAM_KEY_OBJECT_IDS = 'object_ids';
    const PARAM_KEY_ACTION = 'action';
    const PARAM_KEY_CONTEXT = 'context';

    /**
     * @var Logger $logger
     */
    private $logger;

    /**
     * @var ProcessAlertInterface
     */
    private $processAlert;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AlertWorker constructor.
     * @param Logger $logger
     * @param ProcessAlertInterface $processAlert
     * @param EntityManagerInterface $em
     */
    public function __construct(Logger $logger, ProcessAlertInterface $processAlert, EntityManagerInterface $em)
    {
        $this->logger = $logger;
        $this->processAlert = $processAlert;
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::JOB_NAME;
    }

    /**
     * @inheritdoc
     */
    public function run(JobParams $params)
    {
        $objectType = $params->getRequiredParam(ProcessAlertWorker::PARAM_KEY_OBJECT_TYPE);
        $objectIds = $params->getRequiredParam(ProcessAlertWorker::PARAM_KEY_OBJECT_IDS);
        $action = $params->getRequiredParam(ProcessAlertWorker::PARAM_KEY_ACTION);
        $context = $params->getRequiredParam(ProcessAlertWorker::PARAM_KEY_CONTEXT);

        try {
            $this->processAlert->createAlerts($objectType, $objectIds, $action, $context);
        } catch (Exception $exception) {
            $this->logger->error(sprintf('could not create alert, error occur: %s', $exception->getMessage()));
        } finally {
            $this->em->clear();
            gc_collect_cycles();
        }
    }
}