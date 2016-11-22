<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/22
 * Time: 16:04
 */

namespace packer;

use ZPHP\Common\MessagePacker;


class Ant
{
    public static function unpack($data)
    {
        $message = new MessagePacker($data);
        $header = $message->readString();
        $body = $message->readString();
        return [
            json_decode($header, true),
            json_decode($body, true),
        ];
    }

    public static function pack($header, $body)
    {
        $message = new MessagePacker();
        $message->writeString($header);
        $message->writeString($body);
        return $message->getData();
    }
}