<?php

namespace Biddy\Worker\Core\Scheduler;

use Biddy\Worker\Core\JobParams;

interface LinearJobSchedulerInterface
{
    public function addJob($jobs, $linearTubeName, array $extraJobData = [], JobParams $parentJobParams = null, $jobTTR = null);

    public function getNextJobPriority($dataSetId);
}