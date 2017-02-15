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
        $id = $this->dao->add([
            'serviceName' => $serviceName,
            'item' => $item,
            'value' => $value,
        ]);
        if ($id) {
            LoadClass::getService('Sync')->syncId($id);
        }
    }

    public function update($id, $item, $value)
    {
        $ret = $this->dao->update([
            'item' => $item,
            'value' => $value
        ], ['id=' => $id]);
        if ($ret) {
            LoadClass::getService('Sync')->syncId($id);
        }
    }

    public function remove($id)
    {
        $record = $this->dao->fetchById($id);
        if ($record) {
            $ret = $this->dao->remove([
                'id=' => $id
            ]);
            if ($ret) {
                LoadClass::getService('Sync')->removeKey($record->serviceName, $record->item);
            }
        }
    }

    public function removeService($serviceName)
    {
        $ret = $this->dao->remove([
            'serviceName=' => "'{$serviceName}'"
        ]);
        if ($ret) {
            LoadClass::getService('Sync')->removeAll($serviceName);
        }
    }
}