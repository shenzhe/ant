<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/29
 * Time: 10:42
 */

namespace entity;


class ConfigList extends Base
{
    const TABLE_NAME = 'config_list';
    const PK_ID = 'id';

    public $id;
    public $serviceName;
    public $item;
    public $value;
}