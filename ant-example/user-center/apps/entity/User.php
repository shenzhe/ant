<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/3/9
 * Time: 17:54
 */

namespace entity;


class User extends Base
{
    const TABLE_NAME = 'user';
    const PK_ID = 'id';

    public $id;
    public $name;
    public $password;
    public $avatar;

    public function getInfo()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
        ];
    }
}