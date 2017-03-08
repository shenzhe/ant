<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/28
 * Time: 15:41
 */

namespace sdk;

use common\Consts;
use common\Log;
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
        $serviceName = ZConfig::getField('soa', 'serviceName', ZConfig::get('project_name'));
        $value = ZConfig::getField(Utils::getServiceConfigNamespace($serviceName), $key, $default, $throw);
        if (!is_null($value)) {
            return $value;
        }
        try {
            $server = LoadService::getService(Consts::CONFIG_SERVER_NAME);
            $result = $server->call('get', [
                'key' => $key,
                'serviceName' => $serviceName
            ]);
            $body = $result->getBody();
            $record = $body['data']['record'];
            if (empty($record)) {
                throw new MyException("record empty", -1);
            }
            if ($record['item'] !== $key) {
                throw new MyException("key error {$key} != {$record['item']}", -1);
            }
            if (is_array($record['value'])) {
                return $record['value'];
            }
            return json_decode($record['value'], true);
        } catch (\Exception $e) {
            if ($throw) {
                throw $e;
            }
            MyException::exceptionHandler($e);
            return false;
        }
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.

    }
}