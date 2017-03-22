<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/3/9
 * Time: 17:55
 */

namespace dao;


class User extends Base
{
    public function __construct($useDb = 'common')
    {
        parent::__construct('entity\\User', $useDb);
    }
}