<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:41
 */

namespace web;

use ctrl\Base as CBase;
use common\LoadClass;

class main extends CBase
{
    /**
     * @return array
     * @desc 添加key
     */
    public function add()
    {
        $key = $this->getString('key');
        $serviceName = $this->getString('serviceName');
        $value = $this->getString('value');
        /**
         * @var $service \service\ConfigList
         */
        $service = LoadClass::getService('ConfigList');
        $id = $service->add($serviceName, $key, $value);
        return $this->getView([
            'id' => $id
        ]);
    }

    /**
     * @return array
     * @desc 更新key
     */
    public function update()
    {
        $id = $this->getInteger('id');
        $key = $this->getString('key');
        $value = $this->getString('value');
        /**
         * @var $service \service\ConfigList
         */
        $service = LoadClass::getService('ConfigList');
        $ret = $service->update($id, $key, $value);
        return $this->getView([
            'ret' => $ret
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