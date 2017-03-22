<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:51
 */

namespace service;

use common\LoadClass;
use common\Log;
use sdk\HttpClient;
use sdk\TcpClient;
use sdk\UdpClient;
use socket\Udp;
use ZPHP\Socket\Adapter\Swoole;

class Subscriber extends Base
{

    public function subscriber($serviceName, $subscriber)
    {
        if ($serviceName == $subscriber) {
            return;
        }
        $record = LoadClass::getDao('Subscriber')->fetchOne([
            'serviceName=' => "'{$serviceName}'",
            'subscriber=' => "'{$subscriber}'"
        ]);
        if (empty($record)) {
            return LoadClass::getDao('Subscriber')->add([
                'serviceName' => $serviceName,
                'subscriber' => $subscriber,
            ]);
        }
        return $record->id;
    }

    /**
     * @param $serviceInfo \entity\ServiceList
     * @return bool
     */
    public function sync($serviceInfo)
    {
        $subscriberList = LoadClass::getDao('Subscriber')->fetchAll([
            'serviceName=' => "'{$serviceInfo->name}'",
        ]);
        if (empty($subscriberList)) {
            return false;
        }

        foreach ($subscriberList as $subscriber) {
            /**
             * @var $subscriber \entity\Subscriber
             */
            $serviceList = LoadClass::getDao('ServiceList')->fetchAll([
                'name=' => "'{$subscriber->subscriber}'",
            ]);
            if (empty($serviceList)) {
                continue;
            }
            foreach ($serviceList as $sub) {
                /**
                 * @var $sub \entity\ServiceList
                 */
                if (!$sub->status) {
                    continue;  //没有运行
                }
                try {
                    if ($sub->serverType == Swoole::TYPE_TCP) {
                        $service = new TcpClient($sub->ip, $sub->port);
                    } elseif ($sub->serverType == Swoole::TYPE_UDP) {
                        $service = new UdpClient($sub->ip, $sub->port);
                    } elseif ($sub->serverType == Swoole::TYPE_HTTP) {
                        continue;
                    }

                    switch ($sub->serverType) {
                        case Swoole::TYPE_TCP:
                            $service = new TcpClient($sub->ip, $sub->port);
                            break;
                        case Swoole::TYPE_UDP:
                            $service = new UdpClient($sub->ip, $sub->port);
                            break;
                        case Swoole::TYPE_HTTP:
                        case Swoole::TYPE_HTTPS:
                        case Swoole::TYPE_WEBSOCKET:
                        case Swoole::TYPE_WEBSOCKETS:
                            $service = new HttpClient($sub->ip, $sub->port);
                            break;
                    }
                    $service->setApi('antConfigAgent')->call('syncRegister', [
                        'serviceInfo' => $serviceInfo
                    ]);
                } catch (\Exception $e) {
                    //发送错误
                    Log::info([$e->getMessage(), $e->getCode()], 'syncRegister_error');
                }
            }
        }
    }

}