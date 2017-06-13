<?php

namespace ctrl;

use ZPHP\Controller\IController;
use ZPHP\Core\Config as ZConfig;
use common;
use ZPHP\Session\Factory as ZSession;
use ZPHP\Protocol\Request;
use exceptionHandler\ParamException;

abstract class Base implements IController
{
    /**
     * @return bool
     * @throws \Exception
     * @desc 前置控制器，session自动开启判断， 输入参数过滤
     */
    public function _before()
    {
        if (ZConfig::getField('project', 'auto_session', false)) {
            ZSession::start();
        }
        if (!empty($_POST)) {
            $_POST = $this->_filter($_POST);
        }
        if (!empty($_GET)) {
            $_GET = $this->_filter($_GET);
        }
        $params = Request::getParams();
        if (!empty($params)) {
            $params = $this->_filter($params);
            $_REQUEST = $params;
            Request::setParams($params);
        }
        //数据库的超时检测
        common\LoadClass::getDao('Auto')->checkPing();
        return true;
    }

    private function _filter($params)
    {
        foreach ($params as &$val) {
            if (is_array($val)) {
                $val = $this->_filter($val);
            } else {
                $val = addslashes(trim($val));
            }
        }
        unset($val);
        return $params;
    }

    /**
     * @return array
     * @desc 后置控制器
     */
    public function _after()
    {
    }

    /**
     * @param $key                          参数名
     * @param null $default 当没有此参数时的默认值
     * @param array|null $params 数据源（默认 Request::getParams()）
     * @param bool|false $abs 是否取绝对值
     * @param bool|false $notEmpty 是否能为空
     * @return int|null|number
     * @throws ParamException
     * @desc 获取int型参数
     */
    protected function getInteger($key, $default = null, array $params = null, $abs = false, $notEmpty = false)
    {
        if (empty($params)) {
            $params = Request::getParams();
        }
        if (!isset($params[$key])) {
            if (isset($params[$key])) {
                return \intval($params[$key]);
            }
            if ($default !== null) {
                return $default;
            }
            throw new ParamException("no params {$key}", common\ERROR::PARAM_ERROR);
        }
        $integer = isset($params[$key]) ? \intval($params[$key]) : 0;
        if ($abs) {
            $integer = \abs($integer);
        }
        if ($notEmpty && empty($integer)) {
            throw new ParamException('params no empty', common\ERROR::PARAM_ERROR);
        }
        return $integer;
    }

    /**
     * @param $key                          参数名
     * @param null $default 当没有此参数时的默认值
     * @param array|null $params 数据源（默认 Request::getParams()）
     * @param bool|false $abs 是否取绝对值
     * @param bool|false $notEmpty 是否能为空
     * @return int|null|number
     * @throws ParamException
     * @desc 获取浮点型参数
     */
    protected function getFloat($key, $default = null, array $params = null, $abs = true, $notEmpty = false)
    {
        if (empty($params)) {
            $params = Request::getParams();
        }
        if (empty($params[$key])) {
            if (isset($params[$key])) {
                return \floatval($params[$key]);
            }
            if ($default !== null) {
                return $default;
            }
            throw new ParamException("no params {$key}", common\ERROR::PARAM_ERROR);
        }
        $integer = isset($params[$key]) ? \floatval($params[$key]) : 0;
        if ($abs) {
            $integer = \abs($integer);
        }
        if ($notEmpty && empty($integer)) {
            throw new ParamException('params no empty', common\ERROR::PARAM_ERROR);
        }
        return $integer;
    }

    /**
     * @param $key                          参数名
     * @param null $default 当没有此参数时的默认值
     * @param array|null $params 数据源（默认 Request::getParams()）
     * @param bool|false $notEmpty 是否能为空
     * @return int|null|number
     * @throws ParamException
     * @desc 获取字符串参数
     */
    protected function getString($key, $default = null, array $params = null, $notEmpty = true)
    {
        if (empty($params)) {
            $params = Request::getParams();
        }
        if (empty($params[$key])) {
            if (null !== $default) {
                return $default;
            }
            throw new ParamException("no params {$key}", common\ERROR::PARAM_ERROR);
        }
        $string = $params[$key];
        if (!empty($notEmpty) && empty($string)) {
            throw new ParamException('params no empty', common\ERROR::PARAM_ERROR);
        }
        return $string;
    }

    /**
     * @param $key
     * @param null $default
     * @param array|null $params
     * @param bool|true $notEmpty
     * @return array|null
     * @throws ParamException
     * @desc 获取数组类型的参数
     */
    protected function getArray($key, $default = null, array $params = null, $notEmpty = true)
    {
        if (empty($params)) {
            $params = Request::getParams();
        }
        if (empty($params[$key])) {
            if (isset($params[$key])) {
                return [];
            }
            if (null !== $default) {
                return $default;
            }
            throw new ParamException("no params {$key}", common\ERROR::PARAM_ERROR);
        }
        $data = $params[$key];
        if (!empty($notEmpty) && empty($data)) {
            throw new ParamException('params no empty', common\ERROR::PARAM_ERROR);
        }
        return $data;
    }

    /**
     * @param $key
     * @param null $default
     * @param array|null $params
     * @param bool|true $notEmpty
     * @return mixed|null|string
     * @throws ParamException
     * @desc 获取json类型参数，转换为数组
     */
    protected function getJson($key, $default = null, array $params = null, $notEmpty = true)
    {
        if (empty($params)) {
            $params = Request::getParams();
        }
        if (empty($params[$key])) {
            if (isset($params[$key])) {
                return \trim($params[$key]);
            }
            if (null !== $default) {
                return $default;
            }
            throw new ParamException("no params {$key}", common\ERROR::PARAM_ERROR);
        }
        $data = \json_decode($params[$key], true);
        if (!empty($notEmpty) && empty($data)) {
            throw new ParamException('params no empty', common\ERROR::PARAM_ERROR);
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @desc ctrl数据输出
     *       两个特殊变量 _view_mode: 强制指定view输出格式 （默认受 config中view_mode参数控制）
     *                  _tpl_file:  强制指定模版文件     (默认为 ctrl/method.php)
     */
    protected function getView($data = array())
    {
        $result = [
            'code' => 0,
            'msg' => '',
            'data' => $data,
        ];
        if (Request::isAjax()) {
            $result['_view_mode'] = 'Json';
        }
        return $result;
    }
}
