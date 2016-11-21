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
        $api = $this->getString('api');
        $time = $this->getFloat('time');
        common\Log::info('service', [
            $api, $time
        ]);
    }

    /**
     * @desc 服务提供者时间打点
     */
    public function service()
    {
        $api = $this->getString('api');
        $time = $this->getFloat('time');
        common\Log::info('service', [
            $api, $time
        ]);
    }
}