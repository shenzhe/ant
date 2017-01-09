<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 17:48
 */

namespace sdk;

use ZPHP\Client\Rpc\Tcp;
use packer\Ant;
use ZPHP\Client\Rpc\Udp;
use ZPHP\Protocol\Request;
use scheduler\Scheduler;

class UdpClient extends Udp
{
    /**
     * @param $serviceName
     * @param int $timeOut
     * @param array $config
     * @return Tcp
     */
    public static function getService($serviceName, $timeOut = 500, $config = array())
    {
        list($ip, $port) = Scheduler::getService($serviceName);
        try {
            $service = new UdpClient($ip, $port, $timeOut, $config);
            Scheduler::voteGood($serviceName, $ip, $port);
            return $service;
        } catch (\Exception $e) {
            Scheduler::voteBad($serviceName, $ip, $port);
            return self::getService($serviceName, $timeOut, $config);
        }
    }

    public function pack($sendArr)
    {
        return Ant::pack(Request::getHeaders(), $sendArr);
    }

    /**
     * @param $result
     * @return \packer\Result
     */
    public function unpack($result)
    {
        $executeTime = microtime(true) - $this->startTime;
        MonitorClient::clientDot($this->api . DS . $this->method, $executeTime);
        return Ant::unpack($result);
    }
}