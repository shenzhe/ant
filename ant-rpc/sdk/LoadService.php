<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/1/11
 * Time: 21:12
 */

namespace sdk;

use common\Consts;
use ZPHP\Socket\Adapter\Swoole;

class LoadService
{
    /**
     * @param $serviceName  服务名
     * @param string $type 服务类型
     * @param int $timeOut 超时时间
     * @param array $config 配置
     * @param int $isDot 是否上报monitor
     * @param int $retry 重试次数
     * @return TcpClient|\ZPHP\Client\Rpc\Http|\ZPHP\Client\Rpc\Udp
     */
    public static function getService($serviceName, $type = Swoole::TYPE_TCP, $timeOut = 500, $config = array(), $isDot = 1, $retry = 3)
    {
        switch ($type) {
            case Swoole::TYPE_TCP:
                return TcpClient::getService($serviceName, $timeOut, $config, $isDot, $retry);
                break;
            case Swoole::TYPE_UDP:
                return UdpClient::getService($serviceName, $timeOut, $config, $isDot, $retry);
                break;
            case Swoole::TYPE_HTTP:
            case Swoole::TYPE_HTTPS:
                return HttpClient::getService($serviceName, $timeOut, $config, $isDot, $retry);
                break;
            case Swoole::TYPE_WEBSOCKET:
            case Swoole::TYPE_WEBSOCKETS:
                return WSClient::getService($serviceName, $timeOut, $config, $isDot, $retry);
                break;
            default:
                return TcpClient::getService($serviceName, $timeOut, $config, $isDot, $retry);
        }
    }


}