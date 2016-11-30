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
    public static function getService($serviceName, $timeOut = 500, $config = array())
    {
        list($ip, $port) = Scheduler::getService($serviceName);
        return new TcpClient($ip, $port, $timeOut, $config);
    }

    public function pack($sendArr)
    {
        $header = json_encode(Request::getHeaders());
        $body = json_encode($sendArr);
        return Ant::pack($header, $body);
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