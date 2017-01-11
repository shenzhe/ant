<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 17:48
 */

namespace sdk;

use packer\Result;
use ZPHP\Client\Rpc\Http;
use scheduler\Scheduler;

class HttpClient extends Http
{
    public static function getService($serviceName, $timeOut = 500, $config = array())
    {
        list($ip, $port) = Scheduler::getService($serviceName);
        return new HttpClient($ip, $port, $timeOut, $config);
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
        $executeTime = microtime(true) - $this->startTime;
        MonitorClient::clientDot($this->api . DS . $this->method, $executeTime);
        return new Result($headerList, json_decode($body, true));
    }
}