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
        $service = LoadService::getService('api-demo');
        $result = $service->call($method);
        $body = $result->getBody();
        return $body['data'];
    }
}