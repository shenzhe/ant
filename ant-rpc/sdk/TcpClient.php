<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 17:48
 */

namespace sdk;

use common\MyException;
use ZPHP\Client\Rpc\Tcp;
use packer;
use ZPHP\Protocol\Request;
use scheduler\Scheduler;
use ZPHP\Core\Config as ZConfig;

class TcpClient extends Tcp
{
    /**
     * @param $serviceName
     * @param int $timeOut
     * @param array $config
     * @param int $isDot
     * @param int $retry
     * @return TcpClient
     * @throws \Exception
     */
    public static function getService($serviceName, $timeOut = 500, $config = array(), $isDot = 1, $retry = 3)
    {
        try {
            list($ip, $port) = Scheduler::getService($serviceName, $isDot);
            $service = new TcpClient($ip, $port, $timeOut, $config);
            Scheduler::voteGood($serviceName, $ip, $port);
            return $service;
        } catch (\Exception $e) {
            if (!isset($ip, $port) || $retry < 1) {
                throw new MyException($serviceName . ' get error. [' . $e->getMessage() . ']', $e->getCode());
            }
            Scheduler::voteBad($serviceName, $ip, $port);
            $retry--;
            return self::getService($serviceName, $timeOut, $config, $isDot, $retry);
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
        return packer\Factory::getInstance(ZConfig::getField('project', 'packer', 'Ant'))->unpack($result);
    }

    /**
     * @param $method
     * @param array $params
     * @return \packer\Result
     */
    public function call($method, $params = [])
    {
        return parent::call($method, $params);
    }
}