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

    /**
     * [getSecret description]
     * @param  [type] $appId [description]
     * @return [type]        [description]
     */
    public function getSecret($appId)
    {
        $configAppId = Config::get('tools.interface_app_id');
        if ($configAppId !== $appId) {
            return Config::get('tools.interface_secret');
        }

        $cacheKey = Config::get('tools.cache_key');
        $appIdMessage = Cache::get($cacheKey, null);
        if ($appIdMessage) {
            $debug = Config::get('app.debug');
            $appIdMessage = json_decode($appIdMessage, true);
            if ($debug) {
                $appIdMessage = $appIdMessage['test'];
            } else {
                $appIdMessage = $appIdMessage['product'];
            }
            
            Arr::where($appIdMessage, 'eachSign');

        }

        return "";
    }
}