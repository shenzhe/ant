<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/1/11
 * Time: 21:12
 */

namespace sdk;

use ZPHP\Socket\Adapter\Swoole;

class LoadService
{
    /**
     * @param $serviceName
     * @param string $type
     * @param int $timeOut
     * @param array $config
     * @param int $retry
     * @return TcpClient|\ZPHP\Client\Rpc\Http|\ZPHP\Client\Rpc\Udp
     * @throws \Exception
     */
    public static function getService($serviceName, $type = Swoole::TYPE_TCP, $timeOut = 500, $config = array(), $retry = 3)
    {
        switch ($type) {
            case Swoole::TYPE_TCP:
                return TcpClient::getService($serviceName, $timeOut, $config, $retry);
                break;
            case Swoole::TYPE_UDP:
                return UdpClient::getService($serviceName, $timeOut, $config, $retry);
                break;
            case Swoole::TYPE_HTTP:
            case Swoole::TYPE_HTTPS:
                return HttpClient::getService($serviceName, $timeOut, $config, $retry);
                break;
            case Swoole::TYPE_WEBSOCKET:
            case Swoole::TYPE_WEBSOCKETS:
                return WSClient::getService($serviceName, $timeOut, $config, $retry);
                break;
            default:
                return TcpClient::getService($serviceName, $timeOut, $config, $retry);
        }
    }


}