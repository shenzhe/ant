<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:51
 */

namespace service;

use common\LoadClass;
use sdk\TcpClient;
use sdk\UdpClient;
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
                if ($sub->serverType == Swoole::TYPE_TCP) {
                    $service = new TcpClient($sub['ip'], $sub['port']);
                } elseif ($sub['type'] == Swoole::TYPE_UDP) {
                    $service = new UdpClient($sub['ip'], $sub['port']);
                } else {
                    continue;
                }
                $service->setApi('antConfigAgent')->call('syncRegister', $serviceInfo);
            }
        }
    }

}