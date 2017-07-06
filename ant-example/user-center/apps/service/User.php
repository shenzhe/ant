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
use exceptionHandler\UserException;

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
            throw new UserException("user empty", ERROR::USER_EMPTY);
        }
        return $userInfo;
    }

    public function register($name, $password)
    {
        $dao = LoadClass::getDao('User');
        $userInfo = $dao->fetchOne([
            'name=' => "'{$name}'"
        ]);
        if (!empty($userInfo)) {
            throw new UserException("user exists", ERROR::USER_EXISTS);
        }

        $userInfo = new \entity\User();
        $userInfo->name = $name;
        $userInfo->password = $password;
        $id = $dao->add($userInfo);
        if (!$id) {
            throw new UserException("user register error", ERROR::USER_REGISTER_ERROR);
        }
        $userInfo->id = $id;
        return $userInfo;

    }

    public function show($id)
    {
        $dao = LoadClass::getDao('User');
        $userInfo = $dao->fetchById($id);
        if (empty($userInfo)) {
            throw new UserException('user empty', ERROR::USER_NO_EXISTS);
        }
        return $userInfo->getInfo();
    }
}