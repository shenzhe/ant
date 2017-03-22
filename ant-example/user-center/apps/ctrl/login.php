<?php

namespace ctrl;

use common\LoadClass;
use ctrl\Base as CBase;
use sdk\ConfigClient;

class login extends CBase
{

    public function check()
    {
        $name = $this->getString('name');
        $password = $this->getString('password');

        return $this->getView([
            'userInfo' => LoadClass::getService('User')->check($name, $password)
        ]);
    }

    public function register()
    {
        $name = $this->getString('name');
        $password = $this->getString('password');
        return $this->getView([
            'userInfo' => LoadClass::getService('User')->register($name, $password)
        ]);
    }
}