<?php
namespace entity;

use common\VaildInput;

abstract class Base
{
    private $_add_fields = [];
    private $_create = 1;
    protected $_vaild = [];

    /**
     * @param array $data
     * @return bool
     * @desc 自动填充entity
     */
    public function create($data = array())
    {
        if (empty($this->_create)) {
            return false;
        }

        if (empty($data)) {
            $data = $this->getPost();
        }
        $this->_add_fields = array();
        foreach ($data as $key => $val) {
            if (property_exists($this, $key)) {
                if (isset($this->_vaild[$key])) {
                    $val = VaildInput::vaild($val, $this->_vaild[$key]);
                }
                $this->$key = $val;
                $this->_add_fields[] = $key;
            }
        }
    }

    public function noCreate()
    {
        $this->_create = 0;
    }

    public function getFields()
    {
        if (!empty($this->_add_fields)) {
            return $this->_add_fields;
        }
        unset($this->_add_fields);
        unset($this->_create);
        unset($this->_vaild);
        return \array_keys(\get_object_vars($this));
    }

    public function getPkId()
    {
        return self::PK_ID;
    }

    public function getPost()
    {
        return $_POST;
    }
}