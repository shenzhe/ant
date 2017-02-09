<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/29
 * Time: 18:35
 */

namespace ctrl;

use common\LoadClass;
use ZPHP\Core\Config as ZConfig;

class antConfigAgent extends Base
{
    /**
     * @desc 配置下发
     */
    public function sync()
    {

    }

    /**
     * @desc 配置同步
     */
    public function syncAll()
    {
        LoadClass::getService('AntConfigAgent')->syncAll(ZConfig::getField('soa', 'serviceName'));
    }
}