<?php
namespace common;

use ZPHP\Core\Config as ZConfig;
use ZPHP\Protocol\Request;
use ZPHP\Protocol\Response;
use ZPHP\Common\Formater as ZFormater;

/**
 * 异常处理
 *
 * @package service
 *
 */
class MyException extends \Exception
{
    private $realCode = '';
    /**
     * 执行过程中产生的所有异常
     *
     * @var Array
     */
    private static $exceptions = array();

    public function __construct($message, $code = 0)
    {
        $this->realCode = $code;
        parent::__construct($message, $code);
        self::$exceptions[] = $this;
    }

    /**
     * @return int
     * @desc 获取执行过程中的异常发生次数
     */
    public static function getExceptionNum()
    {
        return \count(self::$exceptions);
    }

    /**
     * @return MyException|null
     * @desc 获取执行过程中的发生的最后一次异常
     */
    public static function getLastException()
    {
        return empty(self::$exceptions) ? null : \end(self::$exceptions);
    }

    /**
     * @return mixed
     * @desc 移动最后一个异常
     */
    public static function removeLast()
    {
        return \array_pop(self::$exceptions);
    }

    /**
     * @param \Exception $exception
     * @return mixed
     * @desc 异常处理
     */
    public static function exceptionHandler(\Exception $exception)
    {
        $config = ZConfig::get('project');
        $model = ZFormater::exception($exception, true, false);
        Log::info([\var_export($model, true)], 'exception');

        $info = array();
        if (!empty($exception->realCode)) {
            $codeArr = explode('_', $exception->realCode);
            if (count($codeArr) > 1) {
                $model['code'] = intval($codeArr[0]);
                $model['message'] = $codeArr[1];
            }
        }
        if ($config['debug_mode']) {
            $info['debug'] = $model;
        }
        $info['msg'] = $model['message'];
        $info['code'] = $model['code'];
        return self::display($info, $config['debug_mode']);
    }

    /**
     * @return mixed
     * @throws \Exception
     * @desc fatal error处理
     */
    public static function fatalHandler()
    {
        $error = \error_get_last();
        if (empty($error)) {
            return;
        }
        if (!in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
            return;
        }
        $config = ZConfig::get('project');
        $model = ZFormater::fatal($error);
        if ($config['debug_mode']) {
            $info['debug'] = $model;
            $info['msg'] = $model['message'];
        } else {
            $info['msg'] = 'fatal error';
        }
        Log::info([\var_export($model, true)], 'fatal');
        $info['code'] = $model['code'];
        return self::display($info, $config['debug_mode']);
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @param $errcontext
     * @return mixed
     * @desc  一般错误处理
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (!in_array($errno, [E_RECOVERABLE_ERROR, E_USER_ERROR])) {
            return;
        }
        $error = [
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ];
        $config = ZConfig::get('project');
        $model = ZFormater::fatal($error, true, 'error');
        if ($config['debug_mode']) {
            $info['debug'] = $model;
        }
        Log::info([\var_export($model, true)], 'php_error');
//        $info['msg'] = $model['message'];
//        $info['code'] = $model['code'];
//        return self::display($info, $config['debug_mode']);
        return E_USER_ERROR;
    }

    /**
     * @param $info
     * @param bool $debug
     * @return mixed
     * @desc display输出
     */
    private static function display($info, $debug = false)
    {
        Response::status('200');
        if ('Php' == Request::getViewMode()) {
            if ($debug) {
                Request::setTplFile('public/exception.php');
            } else {
                Request::setTplFile('public/error.php');
            }
        }
        $info['data'] = null;
        return Response::display($info);
    }
}
