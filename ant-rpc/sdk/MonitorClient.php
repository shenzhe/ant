<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/21
 * Time: 11:38
 */

namespace sdk;

use ZPHP\Client\Rpc\Udp;
use scheduler\Scheduler;
use ZPHP\Core\Config as ZConfig;

use common\Consts;


class MonitorClient
{

    /**
     * @param $api
     * @param $time
     * @desc 服务方耗时上报
     */
    public static function serviceDot($api, $time)
    {
        if (ZConfig::getField('project', 'name') == Consts::MONITOR_SERVER_NAME) {
            return;
        }
        list($ip, $port) = Scheduler::getService(Consts::MONITOR_SERVER_NAME);
        $client = new Udp($ip, $port, 3000);
        $client->setApi('dot')->call('service',
            [
                'serviceName' => ZConfig::getField('soa', 'serverName'),
                'serviceIp' => ZConfig::getField('soa', 'serverIp'),
                'servicePort' => ZConfig::getField('soa', 'serverPort'),
                'api' => $api,
                'time' => $time
            ]
        );
    }

    /**
     * @param $api
     * @param $time
     * @desc 调用方耗时上线
     */
    public static function clientDot($api, $time)
    {
        if (ZConfig::getField('project', 'name') == Consts::MONITOR_SERVER_NAME) {
            return;
        }
        list($ip, $port) = Scheduler::getService(Consts::MONITOR_SERVER_NAME);
        $client = new Udp($ip, $port, 3000);
        $client->setApi('dot')->call('client',
            [
                'serviceName' => ZConfig::getField('soa', 'serverName'),
                'serviceIp' => ZConfig::getField('soa', 'serverIp'),
                'servicePort' => ZConfig::getField('soa', 'serverPort'),
                'api' => $api,
                'time' => $time
            ]
        );
    }

    /**
     * @param $api
     * @param $time
     * @desc task任务耗时
     */
    public static function taskDot($api, $time)
    {
        if (ZConfig::getField('project', 'name') == Consts::MONITOR_SERVER_NAME) {
            return;
        }
        list($ip, $port) = Scheduler::getService(Consts::MONITOR_SERVER_NAME);
        $client = new Udp($ip, $port, 3000);
        $client->setApi('dot')->call('task',
            [
                'serviceName' => ZConfig::getField('soa', 'serverName'),
                'serviceIp' => ZConfig::getField('soa', 'serverIp'),
                'servicePort' => ZConfig::getField('soa', 'serverPort'),
                'api' => $api,
                'time' => $time
            ]
        );
    }
}