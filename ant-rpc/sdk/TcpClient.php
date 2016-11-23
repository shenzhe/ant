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
    public function pack($sendArr)
    {
        $header = json_encode(Request::getHeaders());
        $body = json_encode($sendArr);
        return Ant::pack($header, $body);
    }

    public static function getService($serviceName)
    {
        list($ip, $port) = Scheduler::getService($serviceName);
        return new TcpClient($ip, $port);
    }

    public function unpack($result)
    {
        return Ant::unpack($result);
    }
}