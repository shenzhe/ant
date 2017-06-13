<?php

use ZPHP\ZPHP;
use ZPHP\Core\Config;

return array(
    'server_mode' => (PHP_SAPI === 'cli') ? 'Cli' : 'Http',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'lib_path' => array(
        'ant-lib' => ZPHP::getRootPath() . DS . '..' . DS . '..' . DS . 'ant-lib',
        'ant-rpc' => ZPHP::getRootPath() . DS . '..' . DS . '..' . DS . 'ant-rpc',
    ),
    'loadend_hook' => function () {
        Config::mergePath(dirname(__DIR__) . DS . 'public');
    },
    'project' => [
        'default_ctrl_name' => 'main',                      //默认入口控制器
        'debug_mode' => 0,                                  //打开调试模式
        'protocol' => 'Ant',
        'view_mode' => 'Ant',
        'exception_handler' => 'exceptionHandler\BaseException::exceptionHandler',
        'fatal_handler' => 'exceptionHandler\BaseException::fatalHandler',
        'error_handler' => 'exceptionHandler\BaseException::errorHandler',
    ],
);
