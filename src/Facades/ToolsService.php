<?php 

namespace Tools\Api\Facades;

use Illuminate\Support\Facades\Facade;

class ToolsService extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'HaolyyService';
    }

}