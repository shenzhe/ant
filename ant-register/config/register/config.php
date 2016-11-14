<?php
use ZPHP\ZPHP;
use ZPHP\Socket\Adapter\Swoole;

define('KEY_SEPARATOR', '.');

$config = array(
    'server_mode' => 'Socket',
    'project_name' => 'register_center',
    'app_path' => 'apps',
    'ctrl_path' => 'ctrl',
    'lib_path' => array(
        ZPHP::getRootPath() . DS . '..' . DS . 'ant-lib'
    ),
    'project' => [
        'default_ctrl_name' => 'main',                      //默认入口控制器
        'debug_mode' => 0,                                  //打开调试模式
        'protocol' => 'Json',
        'view_mode' => 'Json',
        'exception_handler' => 'common\MyException::exceptionHandler',
        'fatal_handler' => 'common\MyException::fatalHandler',
        'error_handler' => 'common\MyException::errorHandler',
    ],
    'socket' => array(
        'host' => '0.0.0.0',                          //socket 监听ip
        'port' => 9949,                             //socket 监听端口
        'adapter' => 'Swoole',                          //socket 驱动模块
        'server_type' => Swoole::TYPE_TCP,              //socket 业务模型 tcp/udp/http/websocket
        'daemonize' => 0,                             //是否开启守护进程
        'client_class' => 'socket\\Tcp',            //socket 回调类
        'protocol' => 'Json',                         //socket通信数据协议
        'work_mode' => 3,                             //工作模式：1：单进程单线程 2：多线程 3： 多进程
        'worker_num' => 32,                                 //工作进程数
        'task_worker_num' => 32,                                 //工作进程数
        'max_request' => 0,                            //单个进程最大处理请求数
        'addlisten' => array(                           //开启udp监听
            'ip' => '0.0.0.0',
            'port' => 10060
        ),
        'open_length_check' => true,
        'package_length_type' => 'N',
        'package_length_offset' => 0,       //第N个字节是包长度的值
        'package_body_offset' => 4,       //第几个字节开始计算长度
        'package_max_length' => 2000000,  //协议最大长度
    ),
);
$fileList = \ZPHP\Common\Dir::tree(__DIR__ . DS . '..' . DS . 'public', '/\.php$/');
if (!empty($fileList)) {
    foreach ($fileList as $file) {
        $config += include "{$file}";
    }
}
return $config;
