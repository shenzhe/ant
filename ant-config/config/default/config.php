<?php
use ZPHP\ZPHP;
use ZPHP\Socket\Adapter\Swoole;
use ZPHP\Core\Config;
use common\Consts;

return array(
    'server_mode' => 'Cli',
    'project_name' => 'ant-config-center-admin',
    'ctrl_path' => 'web',
    'project' => [
        'debug_mode' => 0,                                  //打开调试模式
    ],
    'lib_path' => array(
        'ant-lib' => ZPHP::getRootPath() . DS . '..' . DS . 'ant-lib',
        'ant-rpc' => ZPHP::getRootPath() . DS . '..' . DS . 'ant-rpc',
    ),
);