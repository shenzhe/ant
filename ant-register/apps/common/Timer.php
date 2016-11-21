<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/17
 * Time: 10:51
 */

namespace common;

use ZPHP\Client\Rpc\Tcp;

class Timer
{
    public static function checkPing()
    {
        /**
         * @var \service\ServiceList
         */
        $service = LoadClass::getService('ServiceList');
        //获取所有的在线服务器
        $allService = $service->fetchAll(['status=' => 1]);
        if (!empty($allService)) {
            foreach ($allService as $item) {
                $rpc = new Tcp($item->host, $item->port);
                try {
                    $result = $rpc->rawCall('ant-ping'); //发送ping包
                    if ('ant-pong' == $result) {
                        continue;
                    }
                } catch (\Exception $e) {
                    //心跳回复失败,设置离线状态
                    $service->update(['status' => 0], ['id=' => $item->id]);
                }
            }
        }
    }
}