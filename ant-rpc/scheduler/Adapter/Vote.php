<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/3/1
 * Time: 11:33
 */

namespace scheduler\Adapter;

use exceptionHandler\SchedulerException;
use scheduler\ISelector;


class Vote implements ISelector
{
    /**
     * @param $serviceName
     * @param $serverList
     * @return array
     * @throws SchedulerException
     */
    public function getOne($serviceName, $serverList)
    {
        $goodList = [];
        foreach ($serverList as $server) {
            if (!$server['status']) {  //服务停止状态
                continue;
            }
            if (isset($server['vote']) && $server['vote'] < 1) { //投票数小于1
                continue;
            }
            $goodList[] = $server;
        }
        if (empty($goodList)) {
            throw new SchedulerException($serviceName . "serverlist empty", -1);
        }
        shuffle($goodList);
        return current($goodList);
    }

    /**
     * @param $serviceInfo
     * @return mixed
     */
    public function success($serviceInfo)
    {
        if (empty($serviceInfo['vote'])) {
            $serviceInfo['vote'] = 1;
        } else {
            $serviceInfo['vote']++;
        }
        return $serviceInfo;
    }

    /**
     * @param $serviceInfo
     * @return mixed
     */
    public function fail($serviceInfo)
    {
        $serviceInfo['vote'] = 0;
        return $serviceInfo;
    }
}