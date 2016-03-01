<?php

namespace Tools\Api\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

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
        $this->comment(PHP_EOL.Inspiring::quote().PHP_EOL);
    }
}
