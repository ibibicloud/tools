<?php

namespace ibibicloud;

// 各种单位格式化
class FormatUnit
{
    // 数字转换为 3.02万
    public function number2CN($number, $decimals = 2)
    {
        if ( $number >= 100000000 ) {
            $yi = floor($number / 100000000);
            $wan = ( $number % 100000000 ) / 10000;
            return $yi . '亿' . number_format($wan, $decimals) . '万';
        }
        if ( $number >= 10000 ) {
            return number_format($number / 10000, $decimals) . '万';
        }

        return $number;
    }

    // 文件大小
    public function fileSize($bytes, $decimals = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        
        return number_format($bytes / pow(1024, $power), $decimals) . ' ' . $units[$power];
    }

    // 时长转换
    public function duration($duration, $isMsSecond = true)
    {
        // 根据 isMsSecond 参数决定是否需要将毫秒转换为秒
        $totalSeconds = $isMsSecond ? $duration / 1000 : $duration;
        
        if ( $totalSeconds < 60 ) {
            // 小于60秒，显示为整数秒
            return floor($totalSeconds) . '秒';
        }
        
        // 计算时 分 秒
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = floor($totalSeconds % 60);
        
        // 生成格式化的时间字符串
        $formatNumber = function($num) {
            return str_pad($num, 2, '0', STR_PAD_LEFT);
        };
        
        return $hours > 0 
            ? "{$hours}:{$formatNumber($minutes)}:{$formatNumber($seconds)}"
            : "{$minutes}:{$formatNumber($seconds)}";
    }

}