<?php

namespace scheduler;

use common\Consts;
use common\LoadClass;
use common\MyException;
use ZPHP\Core\Config as ZConfig;
use sdk\TcpClient;
use ZPHP\ZPHP;

/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/17
 * Time: 15:29
 * @desc 服务调度器
 */
class Scheduler
{
    /**
     * @param $serviceName
     * @param $isDot
     * @return array [$ip, $port]
     * @throws MyException
     * @desc 根据服务名获名一个可用的ip:port
     */
    public static function getService($serviceName, $isDot = 1)
    {

        if (Consts::REGISTER_SERVER_NAME == $serviceName) {
            return [
                ZConfig::getField('socket', 'host'),
                ZConfig::getField('socket', 'port'),
            ];
        }

        $soaConfig = ZConfig::get('soa');
        if (empty($soaConfig)) {
            throw new MyException('soa config empty');
        }

        $serverList = self::getList($serviceName, $soaConfig, $isDot);
        $current = self::getOne($serviceName, $serverList);
        return [
            $current['ip'],
            $current['port']
        ];

    }

    public static function getOne($serviceName, $serverList)
    {
        $goodList = [];
        foreach ($serverList as $server) {
            if (!$server['status']) {  //服务停止状态
                continue;
            }
            if (isset($server['vote']) && $server['vote'] < 0) { //投票数小于1
                continue;
            }
            $goodList[] = $server;
        }
        if (empty($goodList)) {
            throw new MyException($serviceName . "serverlist empty", -1);
        }
        shuffle($goodList);
        return current($goodList);
    }

    public static function getList($serviceName, $soaConfig, $isDot = 1)
    {
        if (ZConfig::get('project_name') === Consts::REGISTER_SERVER_NAME) {
            $serverList = LoadClass::getService('ServiceList')->getServiceList($serviceName);
            if (!empty($serverList)) {
                $serverList = json_decode(json_encode($serverList), true);
            }
            return $serverList;
        }
        $serverList = ZConfig::get($serviceName);
        if (empty($serverList)) {
            $rpcClient = new TcpClient($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
            $data = $rpcClient->setApi('main')->setDot($isDot)->call('getList', [
                'serviceName' => $serviceName,
                'subscriber' => ZConfig::getField('soa', 'serviceName', ZConfig::get('project_name')),
            ]);
            if($data) {
                $data = $data->getData();
                if (!empty($data['serviceList'])) {
                    $serverList = $data['serviceList'];
                    self::reload($serviceName, $serverList);
                }
            }
        }

        if (empty($serverList)) {
            throw new MyException($serviceName . " serverlist empty", -1);
        }
        return $serverList;
    }

    public static function reload($serviceName, $serverList, $rebuild = 1)
    {
        $path = ZConfig::getField('lib_path', 'ant-lib');
        if (empty($path)) {
            return;
        }
        if ($rebuild) {
            foreach ($serverList as $index => $server) {
                $serverList[$server['ip'] . '_' . $server['port'] . '_' . $server['serverType']] = $server;
                unset($serverList[$index]);
            }
        }
        $filename = $path . DS . 'config' . DS . $serviceName . '.php';
        file_put_contents($filename, "<?php\rreturn array(
                        '$serviceName'=>" . var_export($serverList, true) . "
                    );");
        ZConfig::mergeFile($filename);
    }

    /**
     * @param $serviceName
     * @param $ip
     * @param $port
     * @desc rpc调用成功，成功投票+1
     */
    public static function voteGood($serviceName, $ip, $port)
    {
        $soaConfig = ZConfig::get('soa');
        $serverList = self::getList($serviceName, $soaConfig);
        if (!empty($serverList)) {
            foreach ($serverList as $server) {
                if ($server['ip'] == $ip && $server['port'] == $port) {
                    if (empty($server['vote'])) {
                        $server['vote'] = 1;
                    } else {
                        $server['vote']++;
                    }
                    self::reload($serviceName, $serverList);
                    return;
                }
            }
        }
        return;
    }

    /**
     * @param $serviceName
     * @param $ip
     * @param $port
     * @desc rpc调用失败，失败投票 -1
     */
    public static function voteBad($serviceName, $ip, $port)
    {
        $soaConfig = ZConfig::get('soa');
        $serverList = self::getList($serviceName, $soaConfig);
        if (!empty($serverList)) {
            foreach ($serverList as $server) {
                if ($server['ip'] == $ip && $server['port'] == $port) {
                    if (empty($server['vote'])) {
                        $server['vote'] = -1;
                    } else {
                        $server['vote']--;
                    }
                    self::reload($serviceName, $serverList);
                    return;
                }
            }
        }

        return;

    }

}