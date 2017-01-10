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
use ZPHP\Protocol\Request;
use scheduler\Scheduler;

class TcpClient extends Tcp
{
    /**
     * @param $serviceName
     * @param int $timeOut
     * @param array $config
     * @return TcpClient
     */
    public static function getService($serviceName, $timeOut = 500, $config = array())
    {
        list($ip, $port) = Scheduler::getService($serviceName);
        try {
            $service = new TcpClient($ip, $port, $timeOut, $config);
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

    /**
     * @param $method
     * @param array $params
     * @return \packer\Result
     */
    public function call($method, $params = [])
    {
        return parent::call($method, $params);
    }
}