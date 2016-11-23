<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/23
 * Time: 19:26
 */

namespace common;


class Utils
{
    public static function getLocalIp()
    {
        $localIps = \swoole_get_local_ip();
        $interface = ['eth0', 'en0'];
        foreach($interface as $key) {
            if (!empty($localIps[$key])) {
                return $localIps[$key];
            }
        }
        return null;
    }
}