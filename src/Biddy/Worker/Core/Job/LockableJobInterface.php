<?php

namespace Biddy\Worker\Core\Job;

use Biddy\Worker\JobParams;

interface LockableJobInterface extends JobInterface
{
    public function getLockKey(JobParams $params);
}