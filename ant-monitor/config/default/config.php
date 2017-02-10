<?php

use ZPHP\Socket\Adapter\Swoole;
use common\Consts;
use ZPHP\Core\Config;
use ZPHP\ZPHP;

return array(
    'server_mode' => 'Ant',
    'project_name' => 'ant-monitor_center',
    'project' => array(
        'debug_mode' => 1,                                  //打开调试模式
    ),
    'socket' => array(
        'host' => '0.0.0.0',                                //socket 监听ip
        'port' => 8891,                                     //socket 监听端口
        'server_type' => Swoole::TYPE_UDP,                  //socket 业务模型 tcp/udp/http/websocket
        'daemonize' => 1,                                   //是否开启守护进程
        'client_class' => 'socket\\Udp',                    //socket 回调类
        'protocol' => 'Ant',                                //socket通信数据协议
        'work_mode' => 3,                                   //工作模式：1：单进程单线程 2：多线程 3： 多进程
        'worker_num' => 32,                                 //工作进程数
        'task_worker_num' => 32,                            //工作进程数
        'max_request' => 0,                                 //单个进程最大处理请求数
    )
);
