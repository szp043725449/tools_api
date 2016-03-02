<?php

namespace Tools\Api\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use \Config;
use Illuminate\Http\JsonResponse;
use Tools\Api\Sign\ControllerSignInterface;
use \App;
use Illuminate\Support\Arr;
use Tools\Api\Sign\MdSign;

class ApiSign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $configName = 'tools';
        if (\Config::get($configName.'.openMiddlewareSign')) {
            $signData = $request->request->all();
            $appId = $request->request->get('app_id', '');
            $sign = $request->request->get('sign', '');
            if (count($signData)>1 && $appId && $sign) {
                $actionName = \Route::current()->getActionName();
                $actionNameArray = explode('@', $actionName);
                $controller = $actionNameArray[0];
                $controllerClass = App::make($controller);
                $secret = \Config::get($configName.'.secret');
                if ($controllerClass instanceof ControllerSignInterface) {
                    $secret = $controllerClass->getSecret($appId);
                }
                $_sign = Arr::get($signData, 'sign');
                $signData = Arr::except($signData, 'sign');
                $signData = Arr::except($signData, 'secret');
                $mdSign = new MdSign($signData);
                $sign = $mdSign->getSign($secret);
                if (strtoupper($sign) == strtoupper($_sign)) {
                    return $next($request);
                }
            }
            $return = array(
                    'status' => \Config::get($configName.'.signErrorCode'),
                    'message' => \Config::get($configName.'.signErrorMessage'),
                );
            return new JsonResponse(array_filter($return));
        }

        return $next($request);
    }
}
