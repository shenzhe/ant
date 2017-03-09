<?php

namespace socket;

use ZPHP\Socket\Callback\SwooleHttp;

class Http extends SwooleHttp
{
    /**
     * @param $request      \swoole_http_request
     * @param $response     \swoole_http_response
     * @return mixed
     * @desc 收到http数据的业务处理
     */
    public function onRequest($request, $response)
    {
        Handler\Proxy::onRequest($request, $response);
    }

    /**
     * @param $serv //swoole_server对像
     * @param $taskId //task任务id
     * @param $fromId //来自哪个worker
     * @param $data //需要处理的数据
     * @desc task任务，适合处理一些耗时的业务
     */
    public function onTask($serv, $taskId, $fromId, $data)
    {
        $ret = Handler\Proxy::onTask($serv, $taskId, $fromId, $data);
        if (!is_null($ret)) {
            return $ret;
        }
    }

    /**
     * @param $serv //swoole_server对像
     * @param $taskId //task任务id
     * @param $data //task处理之后的结果数据
     * @desc task处理完成之后，数据回调
     */
    public function onFinish($serv, $taskId, $data)
    {
        Handler\Proxy::onFinish($serv, $taskId, $data);
    }

    /**
     * @param $serv //swoole_server对像
     * @param $data //收到的udp数据
     * @param $clientInfo //udp客户端数组
     * @desc 收到udp数据的处理
     */
    public function onPacket($serv, $data, $clientInfo)
    {
        Handler\Proxy::onPacket($serv, $data, $clientInfo);
    }

    /**
     * @param $serv //swoole_server对像
     * @param $workerId //worker或task id ps: id>worker_num是表示是task进程
     * @desc worker/task进程启动后回调，可用于一些初始化业务和操作
     */
    public function onWorkerStart($serv, $workerId)
    {
        opcache_reset();
        parent::onWorkerStart($serv, $workerId);
        Handler\Proxy::onWorkerStart($serv, $workerId);
    }

    /**
     * @param $serv //swoole_server对像
     * @param $workerId //worker/task id
     * @param $workerPid //worker/task系统进程id
     * @param $exitCode //退出错误码
     * @desc  工作进程异常退出之后回调
     */
    public function onWorkerError($serv, $workerId, $workerPid, $exitCode)
    {
        Handler\Proxy::onWorkerError($serv, $workerId, $workerPid, $exitCode);
    }
}

