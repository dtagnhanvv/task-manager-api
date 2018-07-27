<?php

namespace Biddy\Worker\Core\Job;

use Biddy\Worker\Core\JobParams;

interface JobInterface
{
    public function getName();
    public function run(JobParams $params);
}