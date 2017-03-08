<?php

namespace ctrl;

use common\Utils;
use ctrl\Base as CBase;
use ZPHP\Protocol\Response;
use ZPHP\Cache\Factory as ZCache;
use ZPHP\Core\Config as ZConfig;

class qrcode extends CBase
{

    public function login()
    {
        return $this->getView([
            'code' => uniqid('qrcode_'),
            'ws_url' => 'ws://' . Utils::getLocalIp() . ':' . ZConfig::getField('socket', 'port') . '/',
            '_view_mode' => 'Php'
        ]);
    }

    public function wx()
    {
        return $this->getView([
            'code' => $this->getString('code'),
            '_view_mode' => 'Php',
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
        if ($fd) {
            $socket = Request::getSocket();
            $socket->send($fd, Response::display($ret));
        }
        return $ret;
    }
}