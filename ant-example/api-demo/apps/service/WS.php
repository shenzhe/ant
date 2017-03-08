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
    public function bind($code, $fd)
    {
        $cache = ZCache::getInstance('Task');
        $cache->set($code, $fd, 0);
        return true;
    }
}