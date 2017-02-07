<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 16:04
 */

namespace packer\Adapter;

use ZPHP\Common\MessagePacker;
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
            return new Result();
        }
        $message = new MessagePacker($data);
        $header = $message->readString();
        $body = $message->readString();
        return new Result(json_decode($header, true), json_decode($body, true));
    }

    public function pack($header, $body)
    {
        $message = new MessagePacker();
        $message->writeString(json_encode($header));
        $message->writeString(json_encode($body));
        return $message->getData();
    }
}