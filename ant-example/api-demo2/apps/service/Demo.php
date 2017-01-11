<?php

/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/12/27
 * Time: 17:07
 */
namespace service;

use sdk\TcpClient;

class Demo
{
    public function demo($method)
    {
        $service = TcpClient::getService('api-demo2');
        $result = $service->call($method);
        $body = $result->getBody();
        if (!empty($body['code'])) {
            throw new \common\MyException($body['code'] . ':' . $body['msg']);
        }
        return $body['data'];
    }
}