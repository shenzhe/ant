<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:51
 */

namespace service;

use common\LoadClass;
use common\Utils;
use entity;
use ZPHP\Core\Config as ZConfig;

class ServiceList extends Base
{
    /**
     * @var \dao\Base
     */
    protected $dao;

    public function __construct()
    {
        $this->dao = LoadClass::getDao('ServiceList');
    }

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
        $serviceInfo = $this->dao->fetchOne([
            'ip = ' => "'{$serviceIp}'",
            'port = ' => $servicePort
        ]);
        $key = ZConfig::getField('soa', 'ip', $serviceIp) . ":" . ZConfig::getField('soa', 'port', $servicePort);
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
            $id = $this->dao->add($serviceInfo);
            $serviceInfo->id = $id;
        } else if (empty($serviceInfo->status)) {
            if ($serviceInfo->registerKey == $key) {
                $ret = $this->dao->update(['status' => 1, 'startTime' => time(), 'serverType' => $serverType], ['id=' => $serviceInfo->id]);
            } else {
                $ret = $this->dao->update(['status' => 1, 'startTime' => time(), 'registerKey' => $key, 'serverType' => $serverType], ['id=' => $serviceInfo->id]);
            }
            if ($ret) {
                $serviceInfo->status = 1;
            }
        }
        LoadClass::getService('Subscriber')->syncRegister($serviceInfo);
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
        $serviceInfo = $this->dao->fetchOne([
            'ip = ' => "'{$serviceIp}'",
            'port = ' => $servicePort
        ]);

        if (!empty($serviceInfo->status)) {
            if ($this->dao->update(['status' => 0, 'dropTime' => time()], ['id=' => $serviceInfo->id])) {
                $serviceInfo->status = 0;
            }
            LoadClass::getService('Subscriber')->syncRegister($serviceInfo);
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
        return $this->dao->update(['status' => 0, 'dropTime' => time()], ['name=' => $serviceName]);
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