<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/29
 * Time: 10:43
 */

namespace dao;


class ConfigList extends Base
{
    public function __construct($useDb='common')
    {
        parent::__construct('entity\\ConfigList', $useDb);
    }
}