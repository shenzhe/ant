<?php
namespace sdk;

use ZPHP\Client\Rpc\Tcp;
use packer\Ant;
use ZPHP\Protocol\Request;

/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 17:48
 */
class TcpClient extends Tcp
{
    public function pack($sendArr)
    {
        $header = json_encode(Request::getHeaders());
        $body = json_encode($sendArr);
        return Ant::pack($header, $body);
    }
}