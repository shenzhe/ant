<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:51
 */

namespace service;

use common\LoadClass;
use common\Utils;
use entity;
use ZPHP\Core\Config as ZConfig;

class Subscriber extends Base
{
    /**
     * @var \dao\Base
     */
    protected $dao;

    public function __construct()
    {
        $this->dao = LoadClass::getDao('Subscriber');
    }

    public function subscriber($serviceName, $subscriber)
    {
        $record = $this->dao->fetchOne([
            'serviceName=' => "'$serviceName'",
            'subscriber=' => "'$subscriber'"
        ]);
        if (empty($record)) {
            return $this->dao->add([
                'serviceName' => $serviceName,
                'subscriber' => $subscriber,
            ]);
        }
        return $record->id;
    }

}