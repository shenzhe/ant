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

    public function add()
    {

    }
}