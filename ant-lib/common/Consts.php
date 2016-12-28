<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/12/28
 * Time: 11:07
 */

namespace common;


class Consts
{
    const SERVER_TYPE_TCP = 1;
    const SERVER_TYPE_UDP = 2;
    const SERVER_TYPE_HTTP = 4;
    const SERVER_TYPE_WEBSOCKET = 8;


    const REGISTER_SERVER_NAME = 'ant-register-center';
    const MONITOR_SERVER_NAME = 'ant-monitor-center';
    const CONFIG_SERVER_NAME = 'ant-config-center';
}