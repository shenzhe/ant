<?php

/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/12/27
 * Time: 17:07
 */
namespace service;

use sdk\LoadService;

class Demo
{
    public function demo($method)
    {
        //获取一个远程服务
        $service = LoadService::getService('api-demo2');
        //执行服务的方法
        $result = $service->call($method);
        //获取结果
        $body = $result->getBody();
        return $body['data'];
    }
}