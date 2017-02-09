<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/28
 * Time: 15:41
 */

namespace sdk;

use common\Consts;
use common\MyException;
use common\Utils;
use ZPHP\Core\Config as ZConfig;


class ConfigClient
{
    /**
     * @param $key
     * @param $default
     * @param bool $throw
     * @return bool|null
     * @throws \Exception
     * @desc 从配置中心获取一个配置
     */
    public static function get($key, $default, $throw = false)
    {
        $serviceName = ZConfig::getField('soa', 'serviceName');
        $value = ZConfig::getField(Utils::getServiceConfigNamespace($serviceName), $key, $default, $throw);
        if (!is_null($value)) {
            return $value;
        }

        $server = LoadService::getService(Consts::CONFIG_SERVER_NAME);
        $result = $server->call('get', [
            'key' => $key,
            'serviceName' => $serviceName
        ]);
        try {
            $body = $result->getBody();
            return $body['data'];
        } catch (\Exception $e) {
            if ($throw) {
                throw $e;
            }
            return false;
        }
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.

    }
}