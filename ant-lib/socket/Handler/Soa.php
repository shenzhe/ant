<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/10/31
 * Time: 16:48
 */

namespace socket\Handler;

use ZPHP\Core\Config as ZConfig;
use ZPHP\Client\Rpc\Tcp;
use common\MyException;


class Soa
{
    /**
     * @param $server \swoole_server
     * @throws MyException
     * @desc 服务自注册回调
     */
    public static function register($server)
    {
        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            //服务注册
            $rpcClient = new Tcp($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
            $data = $rpcClient->setApi('main')->call('register', [
                'serviceName' => $soaConfig['serviceName'],
                'serviceIp' => $soaConfig['serviceIp'],
                'servicePort' => $soaConfig['servicePort'],
            ]);

            if (empty($data)) {  //注册失败，服务停止
                $server->shutdown();
            }
            echo $data.PHP_EOL;
            $data = json_decode($data, true);

            if(!empty($data['code'])) {
                $server->shutdown();
                throw new MyException($data['msg'], $data['code']);
            }
        }
    }

    /**
     * @param $server \swoole_server
     * @desc  服务下线回调
     */
    public static function drop($server)
    {
        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            //服务注册
            $rpcClient = new Tcp($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
            $rpcClient->setApi('main')->call('drop', [
                'serviceName' => $soaConfig['serviceName'],
                'serviceIp' => $soaConfig['serviceIp'],
                'servicePort' => $soaConfig['servicePort'],
            ]);
        }
    }

}