<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:51
 */

namespace dao;


class Subscriber extends Base
{
    public function __construct($useDb='common')
    {
        parent::__construct('entity\\Subscriber', $useDb);
    }
}