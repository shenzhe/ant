<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/10/31
 * Time: 16:48
 */

namespace socket\Handler;

use ZPHP\Core\Config as ZConfig;
use sdk\TcpClient;
use common\Utils;
use common\MyException;
use common\LoadClass;


class Soa
{
    /**
     * @param $server \swoole_server
     * @throws MyException
     * @desc 服务自注册回调
     */
    public static function register($server)
    {
        //是否自注册
        $isRegisterProject = ZConfig::getField('project', 'is_register_project', 0);
        if ($isRegisterProject) {
            $host = ZConfig::getField('socket', 'host');
            if ('0.0.0.0' == $host) {
                $host = Utils::getLocalIp();
            }
            LoadClass::getService('ServiceList')->register(
                ZConfig::get('project_name'),
                $host,
                ZConfig::getField('socket', 'port')
            );
            return ;
        }

        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            //服务注册
            $rpcClient = new TcpClient($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
            $data = $rpcClient->setApi('main')->call('register', [
                'serviceName' => $soaConfig['serviceName'],
                'serviceIp' => $soaConfig['serviceIp'],
                'servicePort' => $soaConfig['servicePort'],
            ]);

            if (empty($data)) {  //注册失败，服务停止
                $server->shutdown();
            }
            $data = json_decode($data, true);
            if (!empty($data['code'])) {
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
        //是否自下线
        $isRegisterProject = ZConfig::getField('project', 'is_register_project', 0);
        if ($isRegisterProject) {
            $host = ZConfig::getField('socket', 'host');
            if ('0.0.0.0' == $host) {
                $host = Utils::getLocalIp();
            }
            LoadClass::getService('ServiceList')->drop(
                ZConfig::get('project_name'),
                $host,
                ZConfig::getField('socket', 'port')
            );
            return ;
        }

        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            //服务下线
            $rpcClient = new TcpClient($soaConfig['ip'], $soaConfig['port'], $soaConfig['timeOut']);
            $rpcClient->setApi('main')->call('drop', [
                'serviceName' => $soaConfig['serviceName'],
                'serviceIp' => $soaConfig['serviceIp'],
                'servicePort' => $soaConfig['servicePort'],
            ]);
        }
    }

}