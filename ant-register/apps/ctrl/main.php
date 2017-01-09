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
     * @desc 服务注册
     * @method *
     */
    public function register()
    {
        $serviceName = $this->getString('serviceName');
        $serviceIp = $this->getString('serviceIp');
        $servicePort = $this->getInteger('servicePort');
        $serverType = $this->getInteger('serverType');
        /**
         * @var $service \service\ServiceList
         */
        $service = LoadClass::getService('ServiceList');
        return $this->getView([
            'serviceInfo' => $service->register($serviceName, $serviceIp, $servicePort, $serverType)
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
        $service = LoadClass::getService('ServiceList');
        return $this->getView([
            'serviceInfo' => $service->drop($serviceIp, $servicePort)
        ]);
    }

    /**
     * @return array
     * @desc 获取某服务名所有的ip:port
     */
    public function getList()
    {
        $serviceName = $this->getString('serviceName');
        $subscriber = $this->getString('subscriber', '');
        if (!empty($subscriber)) { //添加订阅者
            LoadClass::getService('Subscriber')->subscriber($serviceName, $subscriber);
        }
        /**
         * @var $service \service\ServiceList
         */
        $service = LoadClass::getService('ServiceList');
        return $this->getView([
            'serviceList' => $service->getServiceList($serviceName)
        ]);
    }
}