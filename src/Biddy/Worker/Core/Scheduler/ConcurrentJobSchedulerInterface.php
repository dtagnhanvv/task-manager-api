<?php

namespace Biddy\Worker\Core\Scheduler;

interface ConcurrentJobSchedulerInterface
{
    public function addJob(array $jobs, array $extraJobData = [], $jobTTR = null);
}