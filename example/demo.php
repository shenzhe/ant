<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/1/9
 * Time: 20:25
 */
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ant-rpc' . DIRECTORY_SEPARATOR . 'sdk' . DIRECTORY_SEPARATOR . 'TcpClient.php';

$service = new \TcpClient('10.94.107.22', 7101, 1500);
echo $service->call('test');