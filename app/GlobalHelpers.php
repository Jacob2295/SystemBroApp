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
     * First of all, thanks to this guy on stack overflow: http://stackoverflow.com/a/4248922
     * The idea here is to assume a natural
     *
     * @param array $dataY
     * @param int   $res
     */
    public static function local_min(array $dataY, $radius = 2)
    {
        $maxima = [];

        for ($i = 0; $i < count($dataY); $i += $radius) {
            $maxima[] = max(array_slice($dataY, $i, $radius));
        }

        return $maxima;
    }
}
