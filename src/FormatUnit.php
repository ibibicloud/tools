<?php

declare(strict_types=1);

namespace ibibicloud;

// 各种单位格式化
class FormatUnit
{
    // 数字转换为 9999 9.99万 999.99万 99亿999.99万
    public function number2CN(int $number, int $decimals = 2): string
    {
        // 低于一万直接返回
        if ($number < 10000) {
            return (string)$number;
        }

        // 超过亿：拆分成 亿 + 万
        if ($number >= 100000000) {
            $yi = intdiv($number, 100000000);
            $wan = intdiv($number % 100000000, 10000);
            return $yi . '亿' . number_format($wan, $decimals) . '万';
        }

        // 万到亿之间：显示 x.xx万
        return number_format($number / 10000, $decimals) . '万';
    }

    // 文件大小
    public function fileSize(int $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        
        return number_format($bytes / pow(1024, $power), $decimals) . ' ' . $units[$power];
    }

    // 时长转换
    public function duration(int $duration, bool $isMsSecond = true): string
    {
        // 根据 isMsSecond 参数决定是否需要将毫秒转换为秒
        $totalSeconds = $isMsSecond ? $duration / 1000 : $duration;
        
        if ( $totalSeconds < 60 ) {
            // 小于60秒，显示为整数秒
            return (int)floor($totalSeconds) . '秒';
        }
        
        // 计算时 分 秒
        $hours = (int)floor($totalSeconds / 3600);
        $minutes = (int)floor(($totalSeconds % 3600) / 60);
        $seconds = (int)floor($totalSeconds % 60);
        
        // 生成格式化的时间字符串
        $formatNumber = function($num) {
            return str_pad((string)$num, 2, '0', STR_PAD_LEFT);
        };
        
        return $hours > 0 
            ? "{$hours}:{$formatNumber($minutes)}:{$formatNumber($seconds)}"
            : "{$minutes}:{$formatNumber($seconds)}";
    }

}