<?php
use ZPHP\Socket\Adapter\Swoole;

return array(
    'server_mode' => 'Ant',
    'project_name' => 'ant-register_center',
    'project' => [
        'is_register_project' => 1,                             //是否为注册服务器
        'debug_mode' => 1,                                      //是否打开调试模式
    ],
    'socket' => array(
        'host' => '0.0.0.0',                                    //socket 监听ip
        'port' => 9949,                                         //socket 监听端口
        'server_type' => Swoole::TYPE_TCP,                      //socket 业务模型 tcp/udp/http/websocket
        'daemonize' => 1,                                       //是否开启守护进程
        'worker_num' => 32,                                     //工作进程数
        'task_worker_num' => 32,                                //工作进程数
        'max_request' => 0,                                     //单个进程最大处理请求数
        'addlisten' => array(                                   //开启udp监听
            'ip' => '0.0.0.0',
            'port' => 10060
        )
    ),
    'timer' => array(                                           //定时器
        array(
            'ms' => 5000,                                       //间隔毫秒
            'callback' => 'common\Timer::checkPing'             //回调函数
        ),
    ),
);
