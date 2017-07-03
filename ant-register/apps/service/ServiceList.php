<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:51
 */

namespace service;

use common\ERROR;
use common\LoadClass;
use common\Utils;
use entity;
use exceptionHandler\RegisterException;
use ZPHP\Core\Config as ZConfig;

class ServiceList extends Base
{
    /**
     * @param $serviceName
     * @param $serviceIp
     * @param $servicePort
     * @param $serverType
     * @return entity\ServiceList|mixed
     * @desc 服务注册
     */
    public function register($serviceName, $serviceIp, $servicePort, $serverType)
    {
        /**
         * @var $serviceInfo \entity\ServiceList
         */
        $serviceInfo = LoadClass::getDao('ServiceList')->fetchOne([
            'ip = ' => "'{$serviceIp}'",
            'port = ' => $servicePort
        ]);
        $host = ZConfig::getField('socket', 'host');
        if ('0.0.0.0' == $host) {
            $host = Utils::getLocalIp();
        }
        $key = $host . ":" . ZConfig::getField('socket', 'port');
        if (empty($serviceInfo)) {
            $serviceInfo = new entity\ServiceList();
            $serviceInfo->name = $serviceName;
            $serviceInfo->ip = $serviceIp;
            $serviceInfo->port = $servicePort;
            $serviceInfo->status = 1;
            $serviceInfo->registerTime = time();
            $serviceInfo->startTime = time();
            $serviceInfo->dropTime = 0;
            $serviceInfo->registerKey = $key;
            $serviceInfo->serverType = $serverType;
            $id = LoadClass::getDao('ServiceList')->add($serviceInfo);
            $serviceInfo->id = $id;
        } else if (empty($serviceInfo->status)) {
            if ($serviceInfo->name !== $serviceName) {
                throw new RegisterException(ERROR::SERVICE_NAME_ERROR);
            }
            if ($serviceInfo->registerKey == $key) {
                $ret = LoadClass::getDao('ServiceList')->update([
                    'status' => 1,
                    'startTime' => time(),
                    'serverType' => $serverType
                ], ['id=' => $serviceInfo->id]);
            } else {
                $ret = LoadClass::getDao('ServiceList')->update([
                    'status' => 1,
                    'startTime' => time(),
                    'registerKey' => $key,
                    'serverType' => $serverType
                ], ['id=' => $serviceInfo->id]);
            }
            if ($ret) {
                $serviceInfo->status = 1;
            }
        }
        LoadClass::getService('Subscriber')->sync($serviceInfo);
        return $serviceInfo;
    }

    /**
     * @param $serviceIp
     * @param $servicePort
     * @return mixed
     * @desc 服务摘除
     */
    public function drop($serviceIp, $servicePort)
    {
        $serviceInfo = LoadClass::getDao('ServiceList')->fetchOne([
            'ip = ' => "'{$serviceIp}'",
            'port = ' => $servicePort
        ]);

        if (!empty($serviceInfo->status)) {
            if (LoadClass::getDao('ServiceList')->update(['status' => 0, 'dropTime' => time()], ['id=' => $serviceInfo->id])) {
                $serviceInfo->status = 0;
            }
            LoadClass::getService('Subscriber')->sync($serviceInfo);
        }
        return $serviceInfo;
    }

    /**
     * @param $serviceName
     * @return int
     * @desc 移除某服务所有机器
     */
    public function dropAll($serviceName)
    {
        return LoadClass::getDao('ServiceList')->update(['status' => 0, 'dropTime' => time()], ['name=' => $serviceName]);
    }

    /**
     * @param $serviceName
     * @return mixed
     * @desc 获取服务列表
     */
    public function getServiceList($serviceName)
    {
        $serviceList = LoadClass::getDao('ServiceList')->fetchAll([
            'name=' => "'{$serviceName}'"
        ]);
        return $serviceList;
    }

}