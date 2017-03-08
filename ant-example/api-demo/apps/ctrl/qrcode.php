<?php

namespace ctrl;

use common\Log;
use common\Utils;
use ctrl\Base as CBase;
use ZPHP\Protocol\Request;
use ZPHP\Protocol\Response;
use ZPHP\Cache\Factory as ZCache;
use ZPHP\Core\Config as ZConfig;

class qrcode extends CBase
{

    public function login()
    {
        Request::setViewMode('Php');
        return $this->getView([
            'code' => uniqid('qrcode_'),
            'ws_url' => 'ws://' . Utils::getLocalIp() . ':' . ZConfig::getField('socket', 'port') . '/',
        ]);
    }

    public function wx()
    {
        Request::setViewMode('Php');
        return $this->getView([
            'code' => $this->getString('code'),
        ]);
    }

    public function check()
    {
        $code = $this->getString('code');
        $name = $this->getString('name');

        $ret = $this->getView([
            'name' => $name
        ]);

        $fd = ZCache::getInstance('Task')->get($code);
        Log::info([$code, $fd], 'task_cache');
        if ($fd) {
            $socket = Request::getSocket();
            $socket->push($fd, Response::display($ret));
        }
        return $ret;
    }
}