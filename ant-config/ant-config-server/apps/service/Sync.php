<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/2/5
 * Time: 08:35
 */

namespace service;

use common\Consts;
use common\LoadClass;
use sdk\UdpClient;
use ZPHP\Core\Config as ZConfig;
use sdk\TcpClient;
use ZPHP\Socket\Adapter\Swoole;

class Sync
{
    /**
     * @param $serviceName
     * @desc 同步更新所有的
     */
    public function syncAll($serviceName)
    {
        return $this->syncToClient($serviceName, 'syncAll');
    }

    /**
     * @param $serviceName
     * @param $id
     * @desc 配置变更同步到所有的服务器
     */
    public function syncId($serviceName, $id)
    {
        $record = LoadClass::getService('ConfigList')->fetchById($id);
        if (empty($record)) {
            return;
        }

        return $this->syncToClient($serviceName, 'sync', $record);
    }

    /**
     * @param $serviceName
     * @desc 删除所有的配置
     */
    public function removeAll($serviceName)
    {
        return $this->syncToClient($serviceName, 'removeAll');
    }

    /**
     * @param $serviceName
     * @param $key
     * @desc 删除某个配置
     */
    public function removeKey($serviceName, $key)
    {
        $this->syncToClient($serviceName, 'remove', $key);
    }

    /**
     * @param $serviceName
     * @param $method
     * @param $record
     * @desc 配置同步到所有的client
     */
    private function syncToClient($serviceName, $method, $record)
    {
        $soaConfig = ZConfig::get('soa');
        if (empty($soaConfig)) {
            return;
        }
        $rpcClient = new TcpClient($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
        $data = $rpcClient->setApi('main')->setDot(0)->call('getList', [
            'serviceName' => $serviceName,
        ]);
        $data = $data->getData();
        if (!empty($data['serviceList'])) {
            $serverList = $data['serviceList'];
            foreach ($serverList as $sub) {
                if ($sub['type'] == Swoole::TYPE_TCP) {
                    $service = new TcpClient($sub['ip'], $sub['port']);
                } elseif ($sub['type'] == Swoole::TYPE_UDP) {
                    $service = new UdpClient($sub['ip'], $sub['port']);
                } else {
                    continue;
                }
                $service->setApi('antConfigAgent')->call($method, [
                    'key'=>$record
                ]);
            }
        }
        return;
    }
}