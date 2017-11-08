<?php

namespace ctrl;

use common\Log;
use common\Utils;
use ctrl\Base as CBase;
use sdk\LoadService;
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
        $password = $this->getString('password');

        $service = LoadService::getService('user-center');
        $result = $service->setApi('login')->call('check', [
            'name' => $name,
            'password' => $password
        ]);
        $body = $result->getBody();
        $ret = $this->getView([
            'name' => $body['data']['userInfo']['name']
        ]);

        $fd = ZCache::getInstance('Task')->get($code);
        Log::info([$code, $fd], 'task_cache');
        if ($fd) {
            $socket = Request::getSocket();
            $socket->push($fd, Response::getContent($ret));
        }
        return $ret;
    }

    public function all()
    {
        return $this->getView([
            'cache' => ZCache::getInstance('Task')->all()
        ]);
    }


    public function show()
    {
        $id = $this->getInteger('id');
        $service = LoadService::getService('user-center');
        $result = $service->setApi('main')->call('show', [
            'id' => $id,
        ]);
        $body = $result->getBody();

        return $this->getView([
            'userInfo' => $body['data']['userInfo']
        ]);
    }
}