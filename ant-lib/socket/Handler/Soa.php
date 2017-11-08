<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/10/31
 * Time: 16:48
 */

namespace socket\Handler;

use common\Log;
use ZPHP\Core\Config as ZConfig;
use sdk\TcpClient;
use common\Utils;
use exceptionHandler\SoaException;
use common\LoadClass;


class Soa
{
    /**
     * @param $server
     * @return mixed|string
     * @throws \Exception
     * @desc 服务自注册回调
     */
    public static function register($server)
    {
        //是否自注册
        $isRegisterProject = ZConfig::getField('project', 'is_register_project', 0);
        if ($isRegisterProject) {
            $ip = ZConfig::getField('soa', 'ip', ZConfig::getField('socket', 'host'));
            if ('0.0.0.0' == $ip) {
                $ip = Utils::getLocalIp();
            }
            $port = ZConfig::getField('soa', 'port', ZConfig::getField('socket', 'port'));
            try {
                LoadClass::getService('ServiceList')->register(
                    ZConfig::get('project_name'),
                    $ip,
                    $port,
                    ZConfig::getField('soa', 'serverType', ZConfig::getField('socket', 'server_type'))
                );
            } catch (\Exception $e) {
                $server->shutdown();
                $result = \call_user_func(ZConfig::getField('project', 'exception_handler', 'ZPHP\ZPHP::exceptionHandler'), $e);
                Log::info([ZConfig::get('project_name'), $ip, $port, $result], 'register_error');
                return $result;
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
            $serverPort = ZConfig::getField('soa', 'servicePort', ZConfig::getField('socket', 'port'));
            $data = $rpcClient->setApi('main')->call('register', [
                'serviceName' => $serverName,
                'serviceIp' => $serverIp,
                'servicePort' => $serverPort,
                'serverType' => ZConfig::getField('soa', 'serverType', ZConfig::getField('socket', 'server_type')),
            ]);

            if (empty($data)) {  //注册失败，服务停止
                $server->shutdown();
                Log::info([$serverName, $serverIp, $serverPort], 'register_error');
                return $serverName . ':' . $serverIp . ':' . $serverPort . 'register_error';
            } else {
                try {
                    $data->getBody();
                    //配置同步
                    LoadClass::getService('AntConfigAgent')->syncAll($serverName);
                } catch (\Exception $e) {
                    $server->shutdown();
                    $result = \call_user_func(ZConfig::getField('project', 'exception_handler', 'ZPHP\ZPHP::exceptionHandler'), $e);
                    Log::info([$serverName, $serverIp, $serverPort, $result], 'register_error');
                    return $result;
                }
            }
        }
    }


    /**
     * @param $server
     * @return mixed|void
     * @throws \Exception
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
            $port = ZConfig::getField('socket', 'port');
            $serverName = ZConfig::getField('soa', 'serviceName', ZConfig::get('project_name'));
            try {
                LoadClass::getService('ServiceList')->drop(
                    $host,
                    $port
                );
                return;
            } catch (\Exception $e) {
                $result = \call_user_func(ZConfig::getField('project', 'exception_handler', 'ZPHP\ZPHP::exceptionHandler'), $e);
                Log::info([$serverName, $host, $port, $result], 'drop_error');
                return $result;
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