<?php
namespace common;

use ZPHP\Common\Log as ZLog;

/*
 * 获取配置
 */

class Log
{
    public static function info($data, $file = "info")
    {
        $data[] = posix_getpid();
        ZLog::info($file, $data);
    }
}
