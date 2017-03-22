<?php

namespace ctrl;

use common\LoadClass;
use common\Log;
use ctrl\Base as CBase;

class ws extends CBase
{

    public function open()
    {
        $code = $this->getString('code');
        $fd = $this->getInteger('_fd');
        LoadClass::getService('WS')->open($code, $fd);
        Log::info([$code, $fd], 'task_cache');
    }

    public function close()
    {
        $fd = $this->getInteger('_fd');
        LoadClass::getService('WS')->close($fd);
    }
}