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
    public static function getLocalIp($interface = ['eth0', 'eth1', 'en0'])
    {
        $localIps = \swoole_get_local_ip();
        foreach ($interface as $key) {
            if (!empty($localIps[$key])) {
                return $localIps[$key];
            }
        }
        if (count($localIps) > 0) {
            return current($localIps);
        }
        return null; 
    }

    public static function getServiceConfigNamespace($serviceName)
    {
        return '_'.$serviceName.'_config_';
    }
}