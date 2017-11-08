<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/21
 * Time: 11:38
 */

namespace sdk;

use common\Log;
use common\Utils;
use ZPHP\Common\Formater;
use ZPHP\Core\Config as ZConfig;

use common\Consts;


class MonitorClient
{

    /**
     * @param $api
     * @param $time
     * @throws \Exception
     * @desc 服务方耗时上报
     */
    public static function serviceDot($api, $time)
    {
        if (ZConfig::get('project_name') == Consts::MONITOR_SERVER_NAME ||
            ZConfig::get('project_name') == Consts::REGISTER_SERVER_NAME
        ) {
            return;
        }
        try {
            $client = UdpClient::getService(Consts::MONITOR_SERVER_NAME);
            $serverIp = ZConfig::getField('soa', 'serverIp', ZConfig::getField('socket', 'host'));
            if ('0.0.0.0' == $serverIp) {
                $serverIp = Utils::getLocalIp();
            }
            $client->setApi('dot')->setDot(0)->call('service',
                [
                    'serviceName' => ZConfig::getField('soa', 'serverName', ZConfig::get('project_name')),
                    'serviceIp' => $serverIp,
                    'servicePort' => ZConfig::getField('soa', 'serverPort', ZConfig::getField('socket', 'port')),
                    'api' => $api,
                    'time' => $time
                ]
            );
        } catch (\Exception $e) {
            $model = Formater::exception($e);
            Log::info([\var_export($model, true)], 'exception');
        }
    }

    /**
     * @param $api
     * @param $time
     * @throws \Exception
     * @desc 调用方耗时上线
     */
    public static function clientDot($api, $time)
    {

        $serverIp = ZConfig::getField('soa', 'serverIp', ZConfig::getField('socket', 'host'));
        if ('0.0.0.0' == $serverIp) {
            $serverIp = Utils::getLocalIp();
        }
        $params = [
            'serviceName' => ZConfig::getField('soa', 'serverName', ZConfig::get('project_name')),
            'serviceIp' => $serverIp,
            'servicePort' => ZConfig::getField('soa', 'serverPort', ZConfig::getField('socket', 'port')),
            'api' => $api,
            'time' => $time
        ];
        Log::info($params, 'client_dot');
        if (ZConfig::get('project_name') == Consts::MONITOR_SERVER_NAME ||
            ZConfig::get('project_name') == Consts::REGISTER_SERVER_NAME
        ) {
            return;
        }
        try {
            $client = UdpClient::getService(Consts::MONITOR_SERVER_NAME);
            $client->setApi('dot')->setDot(0)->call('client',
                $params
            );
        } catch (\Exception $e) {
            $model = Formater::exception($e);
            Log::info([\var_export($model, true)], 'exception');
        }
    }

    /**
     * @param $api
     * @param $time
     * @throws \Exception
     * @desc task任务耗时
     */
    public static function taskDot($api, $time)
    {
        if (ZConfig::get('project_name') == Consts::MONITOR_SERVER_NAME ||
            ZConfig::get('project_name') == Consts::REGISTER_SERVER_NAME
        ) {
            return;
        }
        try {
            $client = UdpClient::getService(Consts::MONITOR_SERVER_NAME);
            $serverIp = ZConfig::getField('soa', 'serverIp', ZConfig::getField('socket', 'host'));
            if ('0.0.0.0' == $serverIp) {
                $serverIp = Utils::getLocalIp();
            }
            $client->setApi('dot')->setDot(0)->call('task',
                [
                    'serviceName' => ZConfig::getField('soa', 'serverName', ZConfig::get('project_name')),
                    'serviceIp' => $serverIp,
                    'servicePort' => ZConfig::getField('soa', 'serverPort', ZConfig::getField('socket', 'port')),
                    'api' => $api,
                    'time' => $time
                ]
            );
        } catch (\Exception $e) {
            $model = Formater::exception($e);
            Log::info([\var_export($model, true)], 'exception');
        }
    }
}