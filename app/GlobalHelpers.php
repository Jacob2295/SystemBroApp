<?php

namespace App;

class GlobalHelpers
{

    /**
     * @param     $size
     * @param int $precision
     *
     * @return string
     */
    public static function formatBytes($size, $precision = 2)
    {
        $size = preg_replace("/[^0-9,.]/", "", $size);
        if ($size == 0 || $size == NULL) {
            return "0B";
        }
        $base = log($size) / log(1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[(int)floor($base)];
    }


    /**
     * The idea here is to count the local maximums
     *
     * @param array $dataY
     * @param int   $res
     */
    public static function local_min(array $dataY, $radius = 2)
    {
        $maxima = [];

        for ($i = 0; $i < count($dataY); $i += 1) {
            if (isset($dataY[$i + 1])) {
                if ($dataY[$i] > $dataY[$i + 1]) {
                    $maxima[] = $dataY[$i];
                }
            } else {
                $maxima[] = $dataY[$i];
            }

        }

        return array_sum($maxima);
    }
}