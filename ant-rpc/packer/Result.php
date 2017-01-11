<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/28
 * Time: 15:24
 */

namespace packer;


use common\MyException;

class Result
{
    private $header;
    private $body;

    public function __construct($header = null, $body = null)
    {
        $this->header = $header;
        $this->body = $body;
    }

    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return null|array
     * @throws MyException
     */
    public function getBody()
    {
        if (!empty($this->body['code'])) {
            throw new MyException($this->body['code'] . ':' . $this->body['msg']);
        }
        return $this->body;
    }
}