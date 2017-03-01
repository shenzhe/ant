<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/3/1
 * Time: 11:34
 */

namespace scheduler;


interface ISelector
{
    public function getOne($serviceName, $serverList);

    public function success($serviceInfo);

    public function fail($serverInfo);
}