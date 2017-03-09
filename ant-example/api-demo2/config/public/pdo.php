<?php
return [
    'pdo' => array(
        'common' => array(
            'dsn' => 'mysql:host=127.0.0.1;port=3306',  //dsn地址
            'name' => 'common',                         //自定义名称
            'user' => 'root',                           // db用户名
            'pass' => '123456',                         //db密码
            'dbname' => 'user_center',              //db默认数据库
            'charset' => 'UTF8',                        //db默认编码
            'pconnect' => false,                        //是否开启持久连接,swoole模式必需关闭
            'ping' => 1,                                //是否开始ping检测
            'pingtime' => 7200,                         //ping检测时间
        ),
    ),
];