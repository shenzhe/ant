<?php

namespace scheduler;

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
     * @return array [$ip, $port]
     * @desc 根据服务名获名一个可用的ip:port
     */
    public static function getService($serviceName)
    {
        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            $serverList = self::getList($serviceName, $soaConfig);
            $current = self::getOne($serviceName, $serverList);
            return [
                $current['ip'],
                $current['port']
            ];
        }
    }

    public static function getOne($serviceName, $serverList)
    {
        $goodList = [];
        foreach ($serverList as $server) {
            if ($server['vote'] < 0) {
                continue;
            }
            $goodList[] = $server;
        }
        if (empty($goodList)) {
            throw new MyException($serviceName . "serverlist empty", -1);
        }
        shuffle($goodList);
        return current($serverList);

    }

    public static function getList($serviceName, $soaConfig)
    {
        $serverList = ZConfig::get($serviceName);
        if (empty($serverList)) {
            $rpcClient = new TcpClient($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
            $data = $rpcClient->setApi('main')->call('getList', [
                'serviceName' => $serviceName
            ]);
            $body = $data->getBody();
            if (empty($body['code']) && !empty($body['data']['serviceList'])) {
                $serverList = $body['data']['serviceList'];
                self::reload($serviceName, $serverList);
            }
        }

        if (empty($serverList)) {
            throw new MyException($serviceName . "serverlist empty", -1);
        }
        return $serverList;
    }

    public static function reload($serviceName, $serverList)
    {
        $path = ZPHP::getRootPath() . DS . '..' . DS . 'ant-lib' . DS . 'config';
        $filename = $path . DS . $serviceName . '.php';
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
                    $server['vote']++;
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
                    $server['vote']--;
                    self::reload($serviceName, $serverList);
                    return;
                }
            }
        }

        return;

    }

}