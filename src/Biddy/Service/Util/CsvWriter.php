<?php

namespace Biddy\Service\Util;

class CsvWriter implements CsvWriterInterface
{
    /**
     * @inheritdoc
     */
    public function write($path, $row, $headers)
    {
        if (!file_exists($path)) {
            if (!file_exists(dirname($path))) {
                try {
                    mkdir(dirname($path), 0755, true);
                } catch (\Exception $e) {

                }
            }

            $handle = fopen($path, "a");
            fputcsv($handle, $headers);
        } else {
            $handle = fopen($path, "a");
        }

        fputcsv($handle, $row);
        fclose($handle);
    }
}