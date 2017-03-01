<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 17:48
 */

namespace sdk;

use common\Log;
use common\MyException;
use packer;
use ZPHP\Client\Rpc\Udp;
use ZPHP\Protocol\Request;
use scheduler\Scheduler;
use ZPHP\Core\Config as ZConfig;

class UdpClient extends Udp
{
    /**
     * @param $serviceName
     * @param int $timeOut
     * @param array $config
     * @param int $retry
     * @return Udp
     * @throws \Exception
     */
    public static function getService($serviceName, $timeOut = 500, $config = array(), $retry = 3)
    {
        try {
            list($ip, $port, $type) = Scheduler::getService($serviceName);
            $service = new UdpClient($ip, $port, $timeOut, $config);
            Scheduler::success($serviceName, $ip, $port, $type);
            return $service;
        } catch (\Exception $e) {
            if (!isset($ip, $port) || $retry < 1) {
                throw new MyException($serviceName . ' get error. [' . $e->getMessage() . ']', $e->getCode());
            }
            Scheduler::fail($serviceName, $ip, $port, $type);
            $retry--;
            return self::getService($serviceName, $timeOut, $config, $retry);
        }
    }

    public function pack($sendArr)
    {
        return packer\Factory::getInstance(ZConfig::getField('project', 'packer', 'Ant'))->pack(Request::getHeaders(), $sendArr);
    }

    /**
     * @param $result
     * @return \packer\Result
     */
    public function unpack($result)
    {
        if ($this->isDot) {
            $executeTime = microtime(true) - $this->startTime;
            MonitorClient::clientDot($this->api . DS . $this->method, $executeTime);
        }
        return packer\Factory::getInstance(ZConfig::getField('project', 'packer', 'Ant'))->unpack(null);
    }

    /**
     * @param $method
     * @param array $params
     * @return \packer\Result
     */
    public function call($method, $params = [])
    {
        Request::addHeaders([
            'X-Request-ServerName' => ZConfig::getField('soa', 'service_name', ZConfig::get('project_name')),
            'X-Request-Key' => $this->key,
            'X-Request-TimeOut' => $this->timeOut,
        ], false, true);
        $result = parent::call($method, $params);
        Log::info([$method, $params, $result], 'udp_call');
        return $result;
    }
}