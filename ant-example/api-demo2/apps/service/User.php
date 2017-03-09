<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/3/9
 * Time: 17:56
 */

namespace service;


use common\ERROR;
use common\LoadClass;
use common\MyException;

class User extends Base
{
    public function check($name, $password)
    {
        $dao = LoadClass::getDao('User');
        $userInfo = $dao->fetchOne([
            'name=' => "'{$name}'",
            'password=' => "'{$password}'",
        ]);

        if (empty($userInfo)) {
            throw new MyException("user empty", ERROR::USER_EMPTY);
        }
        return $userInfo;
    }
}