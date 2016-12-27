<?php

/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/12/27
 * Time: 17:07
 */
use sdk\TcpClient;

class Demo
{
    public function demo($method)
    {
        $service = TcpClient::getService('api-demo2');
        $result = $service->call($method);
        if (!empty($result->body['code'])) {
            throw new \common\MyException($result->body['code'] . ':' . $result->body['msg']);
        }
        return $result->body['data'];
    }
}