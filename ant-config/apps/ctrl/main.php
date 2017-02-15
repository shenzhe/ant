<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:41
 */

namespace ctrl;

use ctrl\Base as CBase;
use common\LoadClass;

class main extends CBase
{
    /**
     * @return array
     * @desc 获取某服务某key的配置
     */
    public function get()
    {
        $key = $this->getString('key');
        $serviceName = $this->getString('serviceName');
        /**
         * @var $service \service\ConfigList
         */
        $service = LoadClass::getService('ConfigList');
        $record = $service->fetchOne([
            'serviceName=' => "'{$serviceName}'",
            'key=' => "'{$key}'"
        ]);
        return $this->getView([
            'record' => $record
        ]);
    }

    /**
     * @return array
     * @desc 获取某服务所有的配置
     */
    public function all()
    {
        $serviceName = $this->getString('serviceName');
        return $this->getView([
            'list' => LoadClass::getService('ConfigList')->fetchAll([
                'serviceName=' => "'{$serviceName}'"
            ])
        ]);
    }
}