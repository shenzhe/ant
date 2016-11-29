<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/28
 * Time: 15:24
 */

namespace packer;


class Result
{
    private $header;
    private $body;

    public function __construct($header, $body)
    {
        $this->header = $header;
        $this->body = $body;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getBody()
    {
        return $this->body;
    }
}