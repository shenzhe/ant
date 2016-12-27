<?php
namespace ctrl;
use common\LoadClass;
use ctrl\Base as CBase;

class main extends CBase
{

    public function main()
    {
        return $this->getView([
            'name'=>'demo'
        ]);
    }

    public function test()
    {
        $method = $this->getString('method', 'main');
        return $this->getView(LoadClass::getService('Demo')->demo($method));
    }
}