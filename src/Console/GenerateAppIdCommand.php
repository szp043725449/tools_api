<?php

namespace Tools\Api\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use \Config;
use \Curl;
use Illuminate\Support\Arr;
use \Cache;
use Tools\Api\Sign\MdSign;

class GenerateAppIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:generate:usermessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成接口appid信息';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $interfaceUrl = Config::get('tools.interface_getapimessage_url');
        $appId = Config::get('tools.interface_app_id');
        $secret = Config::get('tools.interface_secret');
        $cacheKey = Config::get('tools.cache_key');
        $mdSign = new MdSign(array('app_id'=>$appId));
        $sign = $mdSign->getSign($secret);
        $contents = Curl::to($interfaceUrl)
                    ->withData(array('app_id'=>$appId, 'sign'=>$sign))
                    ->post();
        Cache::forget($cacheKey);
        Cache::forever($cacheKey, $contents);

        $this->comment("ok".$contents);
    }
}
