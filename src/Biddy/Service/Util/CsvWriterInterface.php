<?php

namespace Biddy\Service\Util;

interface CsvWriterInterface
{
    /**
     * @param $path
     * @param $row
     * @param $headers
     * @return
     */
    public function write($path, $row, $headers);
}