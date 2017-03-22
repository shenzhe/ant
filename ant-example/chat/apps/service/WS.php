<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/3/8
 * Time: 09:30
 */

namespace service;

use ZPHP\Cache\Factory as ZCache;


class WS
{
    public function open($code, $fd)
    {
        $cache = ZCache::getInstance('Task');
        $cache->set($code, $fd, 0);
        $cache->set($fd, $code, 0);
        return true;
    }

    public function close($fd)
    {
        $cache = ZCache::getInstance('Task');
        $code = $cache->get($fd);
        $cache->delete($fd);
        $cache->delete($code);
    }
}