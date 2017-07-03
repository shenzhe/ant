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
    public function demo($method, $params = [])
    {
        //获取服务名为 user-center 的一个远程服务,
        $service = LoadService::getService('user-center');
        //执行服务的方法
        $result = $service->call($method, $params);
        //也可以这么执行
        //$result = $service->{$method}($params);
        //ex:$result = $service->main($params);
        //获取结果
        $body = $result->getBody();
        return $body['data'];
    }

    /**
     * @desc 并行调用示例，
     */
    public function multi()
    {

        $senders = [];

        //执行api1远程调用，返回一个唯一的请求Id
        $service2 = LoadService::getService('user-center');
        $requestId = $service2->multiCall('api1');
        $senders[$requestId] = null;

        $service3 = LoadService::getService('api-demo3');
        $requestId = $service3->multiCall('api1');
        $senders[$requestId] = null;

        $service4 = LoadService::getService('api-demo4');
        $requestId = $service4->multiCall('api1');
        $senders[$requestId] = null;

        //获取执行结果
        $results = $service4->multiReceive();
        $results += $senders;
        return $results;
    }
}