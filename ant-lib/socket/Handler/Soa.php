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
use common\Consts;


class Soa
{
    /**
     * @param $server
     * @throws MyException
     * @throws \Exception
     * @desc 服务自注册回调
     */
    public static function register($server)
    {
        //是否自注册
        $isRegisterProject = ZConfig::getField('project', 'is_register_project', 0);
        if ($isRegisterProject) {
            try {
                $ip = ZConfig::getField('soa', 'ip', ZConfig::getField('socket', 'host'));
                if ('0.0.0.0' == $ip) {
                    $ip = Utils::getLocalIp();
                }
                LoadClass::getService('ServiceList')->register(
                    ZConfig::get('project_name'),
                    $ip,
                    ZConfig::getField('soa', 'port', ZConfig::getField('socket', 'port')),
                    ZConfig::getField('soa', 'serverType', ZConfig::get('project_name'))
                );
                return;
            } catch (\Exception $e) {
                $server->shutdown();
                throw $e;
            }
        }

        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            //服务注册
            if (isset($soaConfig['serviceIp'])) {
                $serverIp = $soaConfig['serviceIp'];
            } else {
                $serverIp = ZConfig::getField('socket', 'host');
                if ('0.0.0.0' == $serverIp) {
                    $serverIp = Utils::getLocalIp();
                }
            }
            $rpcClient = new TcpClient(
                ZConfig::getField('soa', 'ip', null, true),
                ZConfig::getField('soa', 'port', null, true),
                ZConfig::getField('soa', 'timeOut', 3000)
            );
            $serverName = ZConfig::getField('soa', 'serviceName', ZConfig::get('project_name'));
            $data = $rpcClient->setApi('main')->call('register', [
                'serviceName' => $serverName,
                'serviceIp' => $serverIp,
                'servicePort' => ZConfig::getField('soa', 'servicePort', ZConfig::getField('socket', 'port')),
                'serverType' => ZConfig::getField('soa', 'serverType', ZConfig::getField('socket', 'server_type')),
            ]);

            if (empty($data)) {  //注册失败，服务停止
                $server->shutdown();
                throw new MyException($serverName . " register error", -1);
            } else {
                try {
                    $data->getBody();
                    //配置同步
                    LoadClass::getService('AntConfigAgent')->syncAll($serverName);
                } catch (\Exception $e) {
                    $server->shutdown();
                    throw $e;
                }
            }


        }
    }

    /**
     * @param $server
     * @throws \Exception
     * @desc  服务下线回调
     */
    public static function drop($server)
    {
        //是否自下线
        $isRegisterProject = ZConfig::getField('project', 'is_register_project', 0);
        if ($isRegisterProject) {
            try {
                $host = ZConfig::getField('socket', 'host');
                if ('0.0.0.0' == $host) {
                    $host = Utils::getLocalIp();
                }
                LoadClass::getService('ServiceList')->drop(
                    $host,
                    ZConfig::getField('socket', 'port')
                );
                return;
            } catch (\Exception $e) {
                $server->shutdown();
                throw $e;
            }
        }

        $soaConfig = ZConfig::get('soa');
        if (!empty($soaConfig)) {
            //服务下线
            if (isset($soaConfig['serviceIp'])) {
                $serverIp = $soaConfig['serviceIp'];
            } else {
                $serverIp = ZConfig::getField('socket', 'host');
                if ('0.0.0.0' == $serverIp) {
                    $serverIp = Utils::getLocalIp();
                }
            }
            $rpcClient = new TcpClient(
                ZConfig::getField('soa', 'ip', null, true),
                ZConfig::getField('soa', 'port', null, true),
                ZConfig::getField('soa', 'timeOut', 3000)
            );
            $serverName = ZConfig::getField('soa', 'serviceName', ZConfig::get('project_name'));
            $rpcClient->setApi('main')->call('drop', [
                'serviceName' => $serverName,
                'serviceIp' => $serverIp,
                'servicePort' => ZConfig::getField('soa', 'servicePort', ZConfig::getField('socket', 'port')),
            ]);
        }
    }

}