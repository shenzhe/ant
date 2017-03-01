<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/3/1
 * Time: 11:35
 */

namespace scheduler;

use ZPHP\Core\Factory as CFactory;


class Factory
{
    /**
     * @param string $adapter
     * @param null $config
     * @return ISelector
     */
    public static function getInstance($adapter = 'Vote', $config = null)
    {
        $className = __NAMESPACE__ . "\\Adapter\\{$adapter}";
        return CFactory::getInstance($className, $config);
    }
}