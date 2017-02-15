<?php
namespace common;

use ZPHP\Core\Factory;

class LoadClass
{
    /**
     * @param $service
     * @return \service\Base
     * @desc 获取service实例
     */
    public static function getService($service)
    {
        return Factory::getInstance("service\\{$service}");
    }

    /**
     * @param $dao
     * @return \dao\Base
     * @desc 获取dao实例
     */
    public static function getDao($dao)
    {
        $dao = Factory::getInstance("dao\\{$dao}");
        if (method_exists($dao, 'init')) {
            $dao->init();
        }
        return $dao;
    }
}