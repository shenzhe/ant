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

    public function add($serviceName, $item, $value)
    {
        $id = LoadClass::getDao('ConfigList')->add([
            'serviceName' => $serviceName,
            'item' => $item,
            'value' => $value,
        ]);
        if ($id) {
            LoadClass::getService('Sync')->syncId($id);
        }
        return $id;
    }

    public function update($id, $item, $value)
    {
        $ret = LoadClass::getDao('ConfigList')->update([
            'item' => $item,
            'value' => $value
        ], ['id=' => $id]);
        if ($ret) {
            LoadClass::getService('Sync')->syncId($id);
        }
        return $ret;
    }

    public function remove($id)
    {
        $record = LoadClass::getDao('ConfigList')->fetchById($id);
        if ($record) {
            $ret = LoadClass::getDao('ConfigList')->remove([
                'id=' => $id
            ]);
            if ($ret) {
                LoadClass::getService('Sync')->removeKey($record->serviceName, $record->item);
            }
        }
    }

    public function removeService($serviceName)
    {
        $ret = LoadClass::getDao('ConfigList')->remove([
            'serviceName=' => "'{$serviceName}'"
        ]);
        if ($ret) {
            LoadClass::getService('Sync')->removeAll($serviceName);
        }
    }
}