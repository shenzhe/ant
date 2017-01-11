<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 17:48
 */

namespace sdk;

use common\MyException;
use packer\Result;
use ZPHP\Client\Rpc\Http;
use scheduler\Scheduler;

class HttpClient extends Http
{
    /**
     * @param $serviceName
     * @param int $timeOut
     * @param array $config
     * @param int $isDot
     * @param int $retry
     * @return Http
     * @throws \Exception
     */
    public static function getService($serviceName, $timeOut = 500, $config = array(), $isDot = 1, $retry = 3)
    {
        try {
            list($ip, $port) = Scheduler::getService($serviceName, $isDot);
            $service = new HttpClient($ip, $port, $timeOut, $config);
            Scheduler::voteGood($serviceName, $ip, $port);
            return $service;
        } catch (\Exception $e) {
            if ($retry < 1) {
                throw new MyException($serviceName.' get error. ['.$e->getMessage().']', $e->getCode());
            }
            Scheduler::voteBad($serviceName, $ip, $port);
            $retry--;
            return self::getService($serviceName, $timeOut, $config, $isDot, $retry);
        }
    }

    public function pack($sendArr)
    {
        return $sendArr;
    }

    public function unpack($result)
    {

        list($header, $body) = explode("\r\n\r\n", $result, 2);
        $headerArr = explode("\r\n", $header);
        $headerList = [];
        foreach ($headerArr as $str) {
            list($key, $val) = explode(':', $str);
            $headerList[trim($key)] = trim($val);
        }
        if ($this->isDot) {
            $executeTime = microtime(true) - $this->startTime;
            MonitorClient::clientDot($this->api . DS . $this->method, $executeTime);
        }
        return new Result($headerList, json_decode($body, true));
    }
}