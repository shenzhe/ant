<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/17
 * Time: 10:51
 */

namespace common;

use sdk\TcpClient;
use ZPHP\Client\Rpc\Udp;
use ZPHP\Core\Config as ZConfig;

class Timer
{
    public static function checkPing()
    {
        /**
         * @var \service\ServiceList
         * @desc 定时检测在线服务的状态
         */

        $service = LoadClass::getDao('ServiceList');
        //@TODO 需要优化，如果机器比较多，检测会比较慢
        $key = ZConfig::getField('soa', 'ip') . ':' . ZConfig::getField('soa', 'port');
        Log::info(['start', $key], 'ping');
        $allService = $service->fetchAll(['registerKey=' => "'$key'"]);
        if (!empty($allService)) {
            foreach ($allService as $item) {
                try {
                    if ($item->ip . ':' . $item->port == $key) {
                        continue;
                    }
                    if ($item->serverType == Consts::SERVER_TYPE_TCP) {
                        $rpc = new TcpClient($item->ip, $item->port);
                    } elseif ($item->serverType == Consts::SERVER_TYPE_UDP) {
                        //TODO UdpClient暂无
                        continue;
                    } else {
                        continue;
                    }
                    $result = $rpc->rawCall('ant-ping'); //发送ping包
                    Log::info(['success', $item->name, $item->ip, $item->port, $item->status, $result], 'ping');
                    if ('ant-pong' == $result) {
                        if (0 == $item->status) { //离线状态设置为在线状态
                            //@TODO 可以不单条更新，改为批量更新
                            //@TODO 服务上线，通知相关的服务调用方
                            $service->update(['status' => 1], ['id=' => $item->id]);
                        }
                        continue;
                    }
                } catch (\Exception $e) {
                    //心跳回复失败,设置离线状态
                    Log::info(['fail', $item->name, $item->ip, $item->port, $item->status], 'ping');
                    if (1 == $item->status) {
                        //@TODO 可以不单条更新，改为批量更新
                        //@TODO 服务下线，通知相关的服务调用方
                        $service->update(['status' => 0], ['id=' => $item->id]);
                    }
                }
            }
        }
    }
}