<?php

namespace ctrl;

use common\LoadClass;
use ctrl\Base as CBase;
use sdk\ConfigClient;

class main extends CBase
{

    public function main()
    {
        return $this->getView([
            'config' => ConfigClient::get('test'),
            'name' => 'demo2',
        ]);
    }

    public function test()
    {
        $method = $this->getString('method', 'main');
        return $this->getView(LoadClass::getService('Demo')->demo($method));
    }

    public function show()
    {
        $id = $this->getInteger('id');
        return $this->getView([
                'userInfo' => LoadClass::getService('User')->show($id),
            ]
        );
    }
}

