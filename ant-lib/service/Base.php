<?php

namespace service;


use common\LoadClass;

abstract class Base
{
    public function __call($name, $arguments)
    {
        $dao = LoadClass::getDao(substr(get_called_class(), 8));
        if (method_exists($dao, $name)) {
            return call_user_func_array([$dao, $name], $arguments);
        }
    }
}