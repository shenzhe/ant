<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/1
 * Time: 09:48
 */

namespace entity;


class Subscriber extends Base
{
    const TABLE_NAME = 'subscriber';
    const PK_ID = 'id';

    public $id;
    public $serviceName;
    public $subscriber;
}