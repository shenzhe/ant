<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:48
 */

namespace entity;


class ServiceList extends Base
{
    const TABLE_NAME = 'service_list';
    const PK_ID = 'id';

    public $id;
    public $name;               //服务名
    public $ip;                 //ip
    public $port;                 //port
    public $status = 0;                 //运行状态
    public $rate = 0;                 //权重
    public $startTime;                 //启动时间


    public function getIpPort()
    {
        return [
            'ip' => $this->ip,
            'port' => $this->port,
            'status' => $this->status,
            'rate'=>$this->rate,
        ];
    }
}