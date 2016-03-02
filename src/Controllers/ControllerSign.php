<?php

namespace Tools\Api\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Tools\Api\Sign\ControllerSignInterface;
use \Config;
use \Cache;
use Illuminate\Support\Arr;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class ControllerSign extends BaseController implements ControllerSignInterface 
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private static $_appMessage;

    /**
     * [getSecret description]
     * @param  [type] $appId [description]
     * @return [type]        [description]
     */
    public function getSecret($appId)
    {
        $configAppId = Config::get('tools.interface_app_id');
        if ($configAppId == $appId) {
            return Config::get('tools.interface_secret');
        }
        $cacheKey = Config::get('tools.cache_key');
        $appIdMessage = Cache::get($cacheKey, null);
        if ($appIdMessage) {
            $debug = Config::get('app.debug');
            $appIdMessage = json_decode($appIdMessage, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($appIdMessage)) {
                if ($debug) {
                    $appIdMessage = $appIdMessage['test'];
                } else {
                    $appIdMessage = $appIdMessage['product'];
                }
                
                $app = Arr::where($appIdMessage, function($key, $value) use($appId){
                            if ($appId == $value['app_id']) {
                                return true;
                            }

                            return false;
                        });
                if ($app) {
                    self::$_appMessage = $app;
                    return $app[0]['secret'];
                }
            }
        }

        return "";
    }

    public  function getAppMessage()
    {
        return self::$_appMessage;
    }
}