<?php

namespace service;


abstract class Base
{
    /**
     * @var \dao\Base
     */
    protected $dao;

    public function __call($name, $arguments)
    {
        if(method_exists($this->dao, $name)) {
            return call_user_func_array([$this->dao, $name], $arguments);
        }
    }
}