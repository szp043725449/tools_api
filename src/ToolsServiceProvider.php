<?php 

namespace Tools\Api;

use Illuminate\Support\ServiceProvider;
use Tools\Api\ToolsService;
use Tools\Api\Sign\SignService;
use Tools\Api\Middleware\ApiSign;
use Tools\Api\Console\GenerateAppIdCommand;

class ToolsServiceProvider extends ServiceProvider {

    /**
     * Boot service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/tools.php' => config_path('tools.php'),
        ]);
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
            return new HaolyyService();
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
}