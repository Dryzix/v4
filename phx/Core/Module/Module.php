<?php

namespace PHX\Core\Module;

use PHX\Core\Module\ModuleException;
use PHX\Core\Motor;

class Module{

    private $params;

    public function __construct($moduleName)
    {
        $path = Motor::getConf('site')->get('Modules')->path;
        if($path)
        {
            $route = Motor::getConf('site')->get('rootPath').$path.$moduleName.'/module.json';

            if(file_exists($route))
            {
                $this->params = json_decode(file_get_contents($route));
            }
            else
            {
                throw new ModuleException('MISSING module.json FOR MODULE ' . $moduleName);
            }
        }
        else
        {
            throw new ModuleException('MISSING CONFIGURATION FOR MODULES');
        }
    }

    public function getDependencies($key){
        if(isset($this->params->Dependencies->$key))
        {
            return $this->params->Dependencies->$key;
        }
        else
        {
            return [];
        }
    }
}
