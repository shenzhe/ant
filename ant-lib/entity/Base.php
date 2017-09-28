<?php
/**
 * entity基类
 * 可用于表单提交数据自动映射到相应的enity类
 *
 * @package entity
 * @author shenzhe <shenzhe163@gmail.com>
 * @copyright 2013 - 2017 ZPHP Co.
 * @license PHP
 * @link zphp.com
 * @category 
 *
 */
namespace entity;

use common\VaildInput;

abstract class Base
{
    private $_add_fields = [];
    private $_create = 1;
    /**
     * 表单验证规则数组
     *
     * @var array
     */
    protected $_vaild = [];

    /**
     * 自动填充表单
     *
     * @param array $data 数组
     *
     * @return bool
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
    
    /**
     * A summary 不自动创建
     * A *description*
     *
     * @return void
     */
    public function noCreate()
    {
        $this->_create = 0;
    }

    /**
     * 获取字段列表
     *
     * @return array
     */
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

    /**
     * 获取主建名
     *
     * @return string
     */
    public function getPkId()
    {
        return self::PK_ID;
    }

    /**
     * 获取表单post数组
     *
     * @return array
     */
    public function getPost()
    {
        return $_POST;
    }
}