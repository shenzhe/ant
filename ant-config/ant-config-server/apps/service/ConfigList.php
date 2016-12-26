<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/29
 * Time: 10:43
 */

namespace service;

use common\LoadClass;


class ConfigList extends Base
{
    protected $dao;

    public function __construct()
    {
        $this->dao = LoadClass::getDao('ConfigList');
    }

    public function add($serviceName, $item, $value)
    {
        return $this->dao->add([
            'serviceName' => $serviceName,
            'item' => $item,
            'value' => $value,
        ]);
    }

    public function update($id, $item, $value)
    {
        return $this->dao->update([
            'item' => $item,
            'value' => $value
        ], ['id=' => $id]);
    }

    public function remove($id)
    {
        return $this->dao->remove([
            'id=' => $id
        ]);
    }

    public function removeService($serviceName)
    {
        return $this->dao->remove([
            'serviceName=' => $serviceName
        ]);
    }
}