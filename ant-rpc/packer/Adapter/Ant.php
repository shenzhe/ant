<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 16:04
 */

namespace packer\Adapter;

use packer\Result;
use packer\IPacker;


class Ant implements IPacker
{
    /**
     * @param $data
     * @return Result
     */
    public function unpack($data)
    {
        if (empty($data)) {
            return null;
        }
        if (class_exists('swoole_serialize')) {
            $result = swoole_serialize::unpack($data);
        } else {
            $result = json_decode($data, true);
        }
        return new Result($result[0], $result[1]);
    }

    public function pack($header, $body)
    {
        if (class_exists('swoole_serialize')) {
            return swoole_serialize::pack([$header, $body]);
        }
        return json_encode([$header, $body]);
    }
}