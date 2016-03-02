<?php

namespace Tools\Api\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use \Config;
use Illuminate\Http\JsonResponse;
use Tools\Api\Sign\ControllerSignInterface;
use \App;

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
        $actionName = \Route::current()->getActionName();
        $actionNameArray = explode('@', $actionName);
        $controller = $actionNameArray[0];
        $controllerClass = App::make($controller);
        //$controller = 
        $configName = 'tools';
        if (\Config::get($configName.'.openMiddlewareSign')) {
            $signData = $request->request->all();
            $appId = $request->request->get('app_id');
            if (count($signData)>1) {
                $sign = $request->request->get('sign');
                if (!$appId || !$sign) {
                    break;
                }
                $secret = \Config::get($configName.'.secret');
                if ($controllerClass instanceof ControllerSignInterface) {
                    $secret = $controllerClass->getSecret($appId);
                }
                if (empty($secret)) {
                    break;
                }
                unset($signData['secret']);
                $_sign = $signData['sign'];
                unset($signData['sign']);
                ksort($signData);
                reset($signData);
                $signData = $this->paraFilter($signData);
                $sign = $this->buildMysign($signData, $secret);
                if ($sign == strtoupper($_sign)) {
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

    /**
     * 生成签名结果
     * @param $sort_para 要签名的数组
     * @param $key 支付宝交易安全校验码
     * @param $sign_type 签名类型 默认值：MD5
     * return 签名结果字符串
     */
    private function buildMysign($sort_para, $key)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($sort_para);
        //把拼接后的字符串再与安全校验码直接连接起来
        $prestr = $prestr.$key;
        //echo $prestr;exit;
        //把最终的字符串签名，获得签名结果
        $mysgin = md5($prestr);

        return strtoupper($mysgin);
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private function createLinkstring($para)
    {
        $arg  = "";
        foreach($para as $key => $val)
        {
            $arg.=$key."=".$val."&";
        }

        //去掉最后一个&字符
        $arg = trim($arg,'&');

        //如果存在转义字符，那么去掉转义
        if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    private function paraFilter($para)
    {
        $para_filter = array();
        foreach($para as $key => $val)
        {
            if($key == "sign" || $val == "")
            {
                continue;
            }
            else
            {
                $para_filter[$key] = $para[$key];
            }
        }
        return $para_filter;
    }
}
