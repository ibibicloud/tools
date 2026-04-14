<?php

declare(strict_types=1);

namespace ibibicloud;

// 日期格式化
class FormatTime
{
    // 获取 当前月月初 到 下月初 的时间戳范围（毫秒）
    public function getCurrentMonthTimestampRange(bool $isMsSecond = false): int
    {
        // 当前月的第一天 00:00:00
        $startDate = strtotime('first day of this month');
        
        // 下个月的第一天 00:00:00
        $endDate = strtotime('first day of next month');
        
        return [
            // 当前月初1号时间戳 毫秒
            'min_cursor' => ( $isMsSecond ? $startDate * 1000 : $startDate ),
            // 下月月初1号时间戳 毫秒
            'max_cursor' => ( $isMsSecond ? $endDate * 1000 : $endDate )
        ];
    }

}