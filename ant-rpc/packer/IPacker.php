<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2017/2/7
 * Time: 16:48
 */

namespace packer;


interface IPacker
{
    /**
     * @param $data
     * @return mixed
     * @desc 反序列化数据
     */
    public function unpack($data);

    /**
     * @param $header
     * @param $body
     * @return mixed
     * @desc 序列化数据
     */
    public function pack($header, $body);
}