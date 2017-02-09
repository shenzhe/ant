<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2016/11/30
 * Time: 19:54
 */

namespace service;


use common\Consts;
use common\Utils;
use sdk\LoadService;
use ZPHP\Core\Config as ZConfig;

class AntConfigAgent
{

    public function sync($serviceName, $record)
    {
        if (is_string($record['value'])) {
            $record['value'] = json_decode($record['value'], true);
        }
        $this->_sync($serviceName, $record);


    }

    public function syncAll($serviceName)
    {
        if (empty($serviceName)) {
            return false;
        }
        try {
            $configService = LoadService::getService(Consts::CONFIG_SERVER_NAME);
            $result = $configService->call('all', [
                'serviceName' => $serviceName
            ]);
            if (empty($result)) {
                //读取数据失败
                return false;
            }
            $configData = $result->getData();
            $this->_sync($serviceName, $configData);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function _sync($serviceName, $data)
    {
        $path = ZConfig::getField('lib_path', 'ant-lib');
        if (empty($path)) {
            return;
        }
        $serviceName = Utils::getServiceConfigNamespace($serviceName);
        $filename = $path . DS . 'config' . DS . $serviceName . '.php';
        file_put_contents($filename, "<?php\rreturn array(
                        '$serviceName'=>" . var_export($data, true) . "
                    );");
        ZConfig::mergeFile($filename);
    }
}