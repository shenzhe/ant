<?php
use ZPHP\ZPHP;
use ZPHP\Socket\Adapter\Swoole;

return array(
    'server_mode' => 'Ant',
    'project_name' => 'user-center',                      //服务名称
    'project' => [
        'debug_mode' => 0,                              //是否打开调试模式
    ],
    'lib_path' => [
        'ant-lib' => ZPHP::getRootPath() . DS . '..' . DS . '..' . DS . 'ant-lib',
        'ant-rpc' => ZPHP::getRootPath() . DS . '..' . DS . '..' . DS . 'ant-rpc',
    ],
    'socket' => array(
        'host' => '0.0.0.0',                            //socket 监听ip
        'port' => 7001,                                 //socket 监听端口
        'server_type' => Swoole::TYPE_TCP,              //socket 业务模型 tcp/udp/http/websocket
        'daemonize' => 1,                               //是否开启守护进程
        'work_mode' => 3,                               //工作模式：1：单进程单线程 2：多线程 3： 多进程
        'worker_num' => 32,                             //工作进程数
        'task_worker_num' => 32,                        //工作进程数
        'max_request' => 0,                             //单个进程最大处理请求数
        'addlisten' => array(                           //开启udp监听
            'ip' => '0.0.0.0',
            'port' => 7011
        ),
    ),
);
