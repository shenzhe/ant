<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/21
 * Time: 12:05
 */

namespace ctrl;

use common;

class dot extends Base
{

    /**
     * @desc 服务调用者时间打点
     */
    public function client()
    {
        $serverName = $this->getString('serverName', 'undefined');
        $serverIp = $this->getString('serverIp', 'undefined');
        $serverPort = $this->getString('serverPort', 'undefined');
        $api = $this->getString('api', 'undefined');
        $time = $this->getFloat('time', 0);
        common\Log::info('client', [
            $serverName, $serverIp, $serverPort, $api, $time
        ]);
    }

    /**
     * @desc 服务提供者时间打点
     */
    public function service()
    {
        $serverName = $this->getString('serverName', 'undefined');
        $serverIp = $this->getString('serverIp', 'undefined');
        $serverPort = $this->getString('serverPort', 'undefined');
        $api = $this->getString('api', 'undefined');
        $time = $this->getFloat('time', 0);
        common\Log::info('service', [
            $serverName, $serverIp, $serverPort, $api, $time
        ]);
    }
}