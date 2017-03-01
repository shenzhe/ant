<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/30
 * Time: 19:54
 */

namespace service;


use common\Consts;
use common\Log;
use common\Utils;
use scheduler\Scheduler;
use sdk\LoadService;
use ZPHP\Common\Dir;
use ZPHP\Core\Config as ZConfig;
use ZPHP\ZPHP;

class AntConfigAgent
{

    /**
     * @param $serviceName
     * @param $record
     * @return bool
     * @desc 同步一条记录
     */
    public function sync($serviceName, $record)
    {
        if (empty($serviceName)) {
            return false;
        }
        $serviceName = Utils::getServiceConfigNamespace($serviceName);
        $configData = ZConfig::get($serviceName, []);
        $configData[$record['item']] = $record['value'];
        return $this->_sync($serviceName, $configData);
    }

    /**
     * @param $serviceName
     * @param $key
     * @return bool
     * @desc 删除某个配置
     */
    public function remove($serviceName, $key)
    {
        if (empty($serviceName)) {
            return false;
        }
        $serviceName = Utils::getServiceConfigNamespace($serviceName);
        $configData = ZConfig::get($serviceName, []);
        if (isset($configData[$key])) {
            unset($configData[$key]);
            return $this->_sync($serviceName, $configData);
        }
    }

    /**
     * @param $serviceName
     * @return bool
     * @desc 清空所有的配置
     */
    public function removeAll($serviceName)
    {
        if (empty($serviceName)) {
            return false;
        }
        $serviceName = Utils::getServiceConfigNamespace($serviceName);
        $configData = ZConfig::get($serviceName, []);
        if (!empty($configData)) {
            return $this->_sync($serviceName, []);
        }
    }

    /**
     * @param $serviceName
     * @return bool
     * @desc 服务启动时，进行全量同步
     */
    public function syncAll($serviceName)
    {
        if (empty($serviceName)) {
            return false;
        }
        try {
            //同步服务配置
            $configService = LoadService::getService(Consts::CONFIG_SERVER_NAME);
            $result = $configService->call('all', [
                'serviceName' => $serviceName
            ]);
            if (empty($result)) {
                //读取数据失败
                return false;
            }
            $data = $result->getData();
            if ($data && !empty($data['list'])) {
                $configData = [];
                foreach ($data['list'] as $_config) {
                    $configData[$_config['item']] = is_array($_config['value']) ? $_config['value'] : json_decode($_config['value'], true);
                }
                $this->_sync(Utils::getServiceConfigNamespace($serviceName), $configData);
            }

            //同步服务列表
            $dir = ZPHP::getConfigPath() . DS . '..' . DS . 'public';
            $fileList = Dir::tree($dir);
            if ($fileList) {
                foreach ($fileList as $file) {
                    $filename = str_replace($dir . DS, '', $file);
                    if (substr($filename, 0, 0) != 'service_') {  //配置
                        continue;
                    }

                    $serviceName = explode('.', $filename)[0];
                    if ($serviceName) {
                        Scheduler::getListForRpc(str_replace('service_', '', $serviceName));
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $serviceName
     * @param $data
     * @return bool
     * @desc 写入并更新配置文件
     */
    private function _sync($serviceName, $data)
    {
        $path = ZPHP::getConfigPath() . DS . '..' . DS . 'public';
        if (!is_dir($path)) {
            if (!mkdir($path)) {
                Log::info([$path], 'mkdir_error');
                return false;
            }
        }
        $filename = $path . DS . $serviceName . '.php';
        file_put_contents($filename, "<?php\r\nreturn array(
                        '$serviceName'=>" . var_export($data, true) . "
                    );");
        return ZConfig::mergeFile($filename);
    }


    /**
     * @param $serviceInfo
     * @desc 同步注册服务器信息
     */
    public function syncRegister($serviceInfo)
    {
        $serviceName = $serviceInfo['name'];
        $serverList = ZConfig::get($serviceName, []);
        $key = $serviceInfo['ip'] . '_' . $serviceInfo['port'] . '_' . $serviceInfo['serverType'];
        $serverList[$key] = $serviceInfo;
        Scheduler::reload($serviceName, $serverList, 0);
    }
}