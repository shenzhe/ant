<?php
namespace socket\Handler;

use ZPHP\Protocol\Response;
use ZPHP\Protocol\Request;
use ZPHP\Core\Route as ZRoute;
use ZPHP\Core\Config as ZConfig;
use common;
use sdk\MonitorClient as MClient;
use ZPHP\ZPHP;

class Proxy
{
    /**
     * @param $serv
     * @return string
     * @desc 返回全局的唯一的请求id
     */
    private static function getRequestId($serv)
    {
        return sha1(uniqid($serv->worker_pid . '_', true));
    }

    /**
     * @param $serv //swoole_server对像
     * @param $fd //文件描述符
     * @param $from_id //来自哪个reactor线程, 此参数基本用不上
     * @param $data //接收到的tcp数据
     * @return mixed
     * @desc 收到tcp数据的业务处理
     */
    public static function onReceive($serv, $fd, $from_id, $data)
    {
        $startTime = microtime(true);
        common\Log::info([$data, substr($data, 4), $fd], 'proxy_tcp');
        $realData = substr($data, 4);
        if ('ant-ping' === $realData) {  //ping包，强制硬编码，不允许自定义
            return $serv->send(pack('N', 8) . 'ant-pong');  //回pong包
        }
        Request::setRequestTime($startTime);
        Request::addParams('_recv', 1);
        Request::parse($realData);
        $params = Request::getParams();
        $params['_fd'] = $fd;
        Request::setParams($params);

        if (!empty($params['_task'])) {
            //task任务, 回复task的任务id
            $taskId = self::getRequestId($serv);
            $params['taskId'] = $taskId;
            $params['requestId'] = Request::getRequestId();
            $serv->task($params);
            $result = Response::display([
                'code' => 0,
                'msg' => '',
                'data' => [
                    'taskId' => $taskId
                ]
            ]);
            $serv->send($fd, pack('N', strlen($result)) . $result);
        } else {

            if (empty($params['_recv'])) {
                //不用等处理结果，立即回复一个空包，表示数据已收到
                $result = Response::display([
                    'code' => 0,
                    'msg' => '',
                    'data' => null
                ]);
                $serv->send($fd, pack('N', strlen($result)) . $result);
            }

            $result = ZRoute::route();
            common\Log::info([$data, $fd, Request::getCtrl(), Request::getMethod(), $result], 'proxy_tcp');
            if (!empty($params['_recv'])) {
                //发送处理结果
                $serv->send($fd, pack('N', strlen($result)) . $result);
            }
        }
        $executeTime = Response::getResponseTime() - $startTime;  //获取程序执行时间
        MClient::serviceDot(Request::getCtrl() . DS . Request::getMethod(), $executeTime);
    }

    /**
     * @param $request \swoole_http_request
     * @param $response \swoole_http_response
     * @desc http请求回调
     */
    public static function onRequest($request, $response)
    {
        $startTime = microtime(true);
        common\Log::info([$request->get], 'proxy_http');
        $param = [];
        $_GET = $_POST = $_REQUEST = $_COOKIE = $_FILES = null;
        if (!empty($request->get)) {
            $_GET = $request->get;
            $param = $request->get;
        }
        if (!empty($request->post)) {
            $_POST = $request->post;
            $param += $request->post;
        }

        if (!empty($request->cookie)) {
            $_COOKIE = $request->cookie;
        }

        if (!empty($request->files)) {
            $_FILES = $request->files;
        }

        foreach ($request->header as $key => $val) {
            $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $key))] = $val;
        }

        $_REQUEST = $param;

        Request::addParams('_recv', 1);
        Request::parse($param);
        $params = Request::getParams();
        $params['_fd'] = $request->fd;
        Request::setParams($params);

        if (!empty($params['_task'])) {
            //task任务, 回复task的任务id
            $serv = Request::getSocket();
            $taskId = self::getRequestId($serv);
            $params['taskId'] = $taskId;
            $params['requestId'] = Request::getRequestId();
            $serv->task($params);
            $result = Response::display([
                'code' => 0,
                'msg' => '',
                'data' => [
                    'taskId' => $taskId
                ]
            ]);
            $response->end($result);
        } else {
            if (empty($params['_recv'])) {
                //不用等处理结果，立即回复一个空包，表示数据已收到
                $result = Response::display([
                    'code' => 0,
                    'msg' => '',
                    'data' => null
                ]);
                $response->end($result);
            } else {
                $result = ZRoute::route();
                if (!empty($params['_recv'])) {
                    //发送处理结果
                    $response->end($result);
                }
            }
        }

        $executeTime = microtime(true) - $startTime;  //获取程序执行时间
        MClient::serviceDot(Request::getCtrl() . DS . Request::getMethod(), $executeTime);
    }

    /**
     * @param $serv  \swoole_server
     * @param $taskId //任务id
     * @param $fromId //来自哪个worker进程
     * @param $data //数据
     * @desc task任务，适合处理一些耗时的业务
     */
    public static function onTask($serv, $taskId, $fromId, $data)
    {
        $startTime = microtime(true);
        Request::setRequestId($data['requestId']);
        Request::parse($data);
        $result = ZRoute::route();
        if (!empty($data['_recv'])) { //发送回执
            if (!empty($data['udp'])) { //udp请求
                $serv->sendto($data['clientInfo']['address'], $data['clientInfo']['port'], $result);
            } else {
                $serv->send($data['_fd'], pack('N', strlen($result)) . $result);
            }
        }
        $executeTime = microtime(true) - $startTime;
        MClient::taskDot(Request::getCtrl() . DS . Request::getMethod(), $executeTime);
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     * @desc task处理完成之后，数据回调
     */
    public static function onFinish($serv, $taskId, $data)
    {

    }

    /**
     * @param $serv
     * @param $data
     * @param $clientInfo
     * @desc 收到udp数据的处理
     */
    public static function onPacket(\swoole_server $serv, $data, $clientInfo)
    {
        $startTime = microtime(true);
        common\Log::info([$data, $clientInfo], 'proxy_udp');
        if ('ant-ping' == $data) {
            $serv->sendto($clientInfo['ip'], $clientInfo['port'], 'ant-pong');
            return;
        }
        $params = Request::parse($data);
        $params['_fd'] = $fd = unpack('L', pack('N', ip2long($clientInfo['address'])))[1];
        if (!empty($params['_task'])) {
            //task任务, 回复task的任务id
            $params['udp'] = 1;
            $params['clientInfo'] = $clientInfo;
            $taskId = self::getRequestId($serv);
            $params['taskId'] = $taskId;
            $params['requestId'] = Request::getRequestId();
            $serv->task($params);
            $result = Response::display([
                'code' => 0,
                'msg' => '',
                'data' => ['taskId' => $taskId]
            ]);
            $serv->sendto($clientInfo['ip'], $clientInfo['port'], $result);
        } else {
            $result = ZRoute::route();
            $serv->sendto($clientInfo['ip'], $clientInfo['port'], $result);
            common\Log::info([$data, $clientInfo, Request::getCtrl(), Request::getMethod(), $result], 'proxy_tcp');
        }

        $executeTime = microtime(true) - $startTime;  //获取程序执行时间
        MClient::serviceDot(Request::getCtrl() . DS . Request::getMethod(), $executeTime);
    }

    /**
     * @param $serv
     * @param $workerId
     * @desc worker/task进程启动后回调，可用于一些初始化业务和操作
     */
    public static function onWorkerStart($serv, $workerId)
    {
        \register_shutdown_function(function () use ($serv) {
            $params = Request::getParams();
            Request::setViewMode(ZConfig::getField('project', 'view_mode', 'Json'));
            common\Log::info([$params], 'shutdown');
            $result = \call_user_func(ZConfig::getField('project', 'fatal_handler', 'ZPHP\ZPHP::fatalHandler'));
            if (!empty($params['_recv'])) { //发送回执
                common\Log::info([$params, $result], 'shutdown');
                $serv->send(Request::getFd(), pack('N', strlen($result)) . $result);
                //@TODO 异常上报
            }
        });
        common\Log::info([$workerId], 'info');
        $timer = ZConfig::get('timer', []);
        if (!empty($timer) && 0 === intval($workerId)) {
            common\Log::info(['timer', $workerId], 'info');
            foreach ($timer as $index => $item) {
                if (!empty($item['ms']) &&
                    !empty($item['callback']) &&
                    \is_callable($item['callback'])
                ) {
                    common\Log::info([$item, $workerId], 'info');
                    \swoole_timer_tick($item['ms'], $item['callback'], isset($item['params']) ? $item['params'] : null);
                }
            }
        }

        $reloadPath = ZConfig::getField('project', 'reload_path', []);
        $reloadPath += [
            ZConfig::getField('lib_path', 'ant-lib'),
            ZPHP::getConfigPath() . DS . '..' . DS . 'public'
        ];
        if (is_array($reloadPath)) {
            foreach ($reloadPath as $path) {
                ZConfig::mergePath($path);
            }
        }
    }

    /**
     * @param $serv
     * @param $workerId
     * @param $workerPid
     * @param $exitCode
     * @desc  工作进程退出之后
     */
    public static function onWorkerError($serv, $workerId, $workerPid, $exitCode)
    {
    }

}

