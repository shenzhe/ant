<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:41
 */

namespace ctrl;

use ctrl\Base as CBase;
use common\loadClass;

class main extends CBase
{

    /**
     * @return array
     * @desc 服务注册
     */
    public function register()
    {
        $serviceName = $this->getString('serviceName');
        $serviceIp = $this->getString('serviceIp');
        $servicePort = $this->getInteger('servicePort');
        /**
         * @var $service \service\ServiceList
         */
        $service = loadClass::getService('ServiceList');
        return $this->getView([
            'serviceInfo'=>$service->register($serviceName, $serviceIp, $servicePort)
        ]);
    }

    public function drop()
    {
//        $serviceName = $this->getString('serviceName');
        $serviceIp = $this->getString('serviceIp');
        $servicePort = $this->getInteger('servicePort');
        /**
         * @var $service \service\ServiceList
         */
        $service = loadClass::getService('ServiceList');
        return $this->getView([
            'serviceInfo'=>$service->drop($serviceIp, $servicePort)
        ]);
    }

    /**
     * @return array
     * @desc 获取某服务名所有的ip:port
     */
    public function getList()
    {
        $serviceName = $this->getString('serviceName');
        /**
         * @var $service \service\ServiceList
         */
        $service = loadClass::getService('ServiceList');
        return $this->getView([
            'serviceList'=>$service->getServiceList($serviceName)
        ]);
    }
}