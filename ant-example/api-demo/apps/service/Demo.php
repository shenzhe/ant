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
    public function demo($method, $params=[])
    {
        //获取服务名为 api-demo2 的一个远程服务,
        $service = LoadService::getService('api-demo2');
        //执行服务的方法
        $result = $service->call($method, $params);
        //也可以这么执行
        //$result = $service->{$method}($params);
        //ex:$result = $service->main($params);
        //获取结果
        $body = $result->getBody();
        return $body['data'];
    }
}