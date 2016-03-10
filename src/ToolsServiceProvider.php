<?php 

namespace Tools\Api;

use Illuminate\Support\ServiceProvider;
use Tools\Api\ToolsService;
use Tools\Api\Sign\SignService;
use Tools\Api\Middleware\ApiSign;
use Tools\Api\Console\GenerateAppIdCommand;
use Validator;

class ToolsServiceProvider extends ServiceProvider {

    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/tools.php' => config_path('tools.php'),
        ]);

        //注册验证方式
        $this->registerCheckMobileValidator();
        $this->registerCheckIdCardValidator();     
        $this->registerCheckNameValidator();
        $this->registerCheckPasswordValidator();
    }

    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @return void
     */
    public function register()
    {
        $this->registerService();
        $this->registerMiddleware();
        $this->registerConsoleCommands();
    }

    /**
     * Setup the entity manager
     */
    protected function registerMiddleware()
    {
        \Route::middleware('hsign', ApiSign::class);
    }  

    /**
     * Setup the entity manager
     */
    protected function registerService()
    {
        $this->app->singleton('toolsService', function ($app) {
            return new ToolsService();
        });
        $this->app->alias('hs', ToolsService::class);
    }  

    /**
     * Setup the entity manager
     */
    protected function registerSign()
    {
        // Bind the default Entity Manager
        $this->app->singleton('hsign', function ($app) {
            return new SignService();
        });
    }

    /**
     * Register console commands
     */
    protected function registerConsoleCommands()
    {
        $this->commands([
            GenerateAppIdCommand::class,
        ]);
    }

    /**
     * [registerCheckMobileValidator description]
     * @return [type] [description]
     */
    protected function registerCheckMobileValidator()
    {
        return Validator::extend('haolyyMobile', function($attribute, $value, $parameters, $validator) {
            //判断长度11位，全数字
            if (!is_numeric($value) || mb_strlen($value) != 11) {
                return false;
            }
            //判断1开头，第二位非0、1、2
            if (!preg_match("/(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/", $value)) {
                return false;
            }
            return true;
        });
    }

    /**
     * [registerIdCcard description]
     * @return [type] [description]
     */
    protected function registerCheckIdCardValidator()
    {
        return Validator::extend('haolyyIdCard', function($attribute, $value, $parameters, $validator) {
            $vCity = [
                '11', '12', '13', '14', '15', '21', '22',
                '23', '31', '32', '33', '34', '35', '36',
                '37', '41', '42', '43', '44', '45', '46',
                '50', '51', '52', '53', '54', '61', '62',
                '63', '64', '65', '71', '81', '82', '91'
            ];

            $idcard = $value;
        
            if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $idcard)) {
                return false;
            }

            if (!in_array(substr($idcard, 0, 2), $vCity)) {
                return false;
            }

            $idcard = preg_replace('/[xX]$/i', 'a', $idcard);
            $vLength = strlen($idcard);
            
            if ($vLength == 18) {
                $vBirthday = substr($idcard, 6, 4) . '-' . substr($idcard, 10, 2) . '-' . substr($idcard, 12, 2);
            } else {
                $vBirthday = '19' . substr($idcard, 6, 2) . '-' . substr($idcard, 8, 2) . '-' . substr($idcard, 10, 2);
            }

            if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) {
                return false;
            }

            if ($vLength == 18) {
                $vSum = 0;

                for ($i = 17; $i >= 0; $i--) {
                    $vSubStr = substr($idcard, 17 - $i, 1);
                    $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
                }

                if ($vSum % 11 != 1) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * [registerCheckMobileValidator 验证姓名]
     * @return [type] [description]
     */
    protected function registerCheckNameValidator()
    {
        return Validator::extend('haolyyName', function($attribute, $value, $parameters, $validator) {
            $patternString = "/^[\x{4e00}-\x{9fa5}·]+$/u";
            return preg_match($patternString, $value);
        });
    }

    /**
     * [checkPassword 验证密码是否合法]
     * @param  [string] $password [密码]
     * @return [bool]           [验证结果]
     */
    protected function registerCheckPasswordValidator()
    {
        return Validator::extend('haolyyPassword', function($attribute, $value, $parameters, $validator) {
            $plen = mb_strlen($value);
            //必须英文加数字，6到12位
            if (preg_match("/(?!^(\d+|[a-zA-Z]+|[w~!@#$%._]+)$)^[\w~!@#$%._?]+$/i", $value) && $plen >= 6 && $plen <= 12) {
                return true;
            } else {
                return false;
            }
        });

    }
}