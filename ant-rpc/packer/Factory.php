<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 *
 */
namespace packer;

use ZPHP\Core\Factory as CFactory;

class Factory
{
    /**
     * @param string $adapter
     * @return IPacker
     * @throws \Exception
     */
    public static function getInstance($adapter = 'Ant')
    {
        $className = __NAMESPACE__ . "\\Adapter\\{$adapter}";
        return CFactory::getInstance($className);
    }
}