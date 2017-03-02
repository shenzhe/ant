<?php

namespace service;


use common\LoadClass;

abstract class Base
{
    public function __call($name, $arguments)
    {
        $dao = LoadClass::getDao($name);
        if (method_exists($dao, $name)) {
            return call_user_func_array([$dao, $name], $arguments);
        }
    }
}