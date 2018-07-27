<?php

namespace Biddy\Worker\Core;

interface JobExpirerInterface
{
    public function expireJobsInTube($linearTube, int $time);

    public function isExpired(string $linearTube, int $time);
}