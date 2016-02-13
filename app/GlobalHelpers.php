<?php

namespace App;

class GlobalHelpers
{

    /**
     * Formats bytes to human readable size
     *
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
     * The idea here is to count the local maximums in order
     * to account for spontaneous loss of TX and RX
     *
     * @param array $dataY
     * @param int   $radius
     * @return array
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

    /**
     * @see formatBytes
     *
     * @param array $bytes
     * @return array
     */
    public static function arrFormatBytes(array $bytes)
    {
        $result = [];

        foreach($bytes as $key => $byte) {
            $result[$key] = GlobalHelpers::formatBytes($byte);
        }

        return $result;

    }

    // thanks to this guy on SO http://stackoverflow.com/a/19680778
    public static function secondsToTime($seconds) {
    $dtF = new \DateTime("@0");
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}
    
}
