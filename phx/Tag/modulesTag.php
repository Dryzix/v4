<?php
/**
 * Class bTag
 *
 * @package fr.phx
 * @license MPL2
 */


namespace PHX\Tag;
use PHX\Core\Module\Module;
use PHX\Core\Motor;

/**
 * Tag {b}
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class modulesTag implements Tag{

    private static $modulesNames = [];
    private static $modules = [];

    /**
     * Lance le tag
     *
     * @param string $subTag
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function run($subTag, $content, $options = null)
    {
        $method = 'run'.ucfirst($subTag);
        return self::$method($options);
    }

    /**
     * Tag {modules}
     *
     * Charge divers modules
     *
     * @param array $options
     * @return string
     */
    private static function runModules($options){
        if(isset($options['list']) || isset($options['l']))
        {
            $list = isset($options['list']) ? $options['list'] : $options['l'];
            if(is_array($list))
            {
                self::$modulesNames = array_merge(self::$modulesNames,$list);
            }
            else
            {
                self::$modulesNames = array_merge(self::$modulesNames,[$list]);
            }

            return '';
        }
        else
        {
            return 'ICORRECT PARAM FOR TAG modules : MISSIGN list (or l) ARG';
        }
    }

    private static function loadModules(){
        foreach(self::$modulesNames as $moduleName)
        {
            if(!isset(self::$modules[$moduleName]))
            {
                self::$modules[$moduleName] = new Module($moduleName);
            }
        }
    }

    public static function runModulesCss($options)
    {
        self::loadModules();
        $path = Motor::getConf('site')->get('Modules')->path;
        $url = rtrim(Motor::getConf('site')->get('siteUrl'), '/') . '/';

        $ret = '';

        foreach(self::$modules as $moduleName => $module)
        {
            $moduleDir = $url.$path.$moduleName.'/';
            foreach($module->getDependencies('css') as $dependency)
            {
                $ret .= '<link rel="stylesheet" href="'.$moduleDir.$dependency.'"/>';
            }
        }

        return $ret;
    }

    public static function runModulesJs($options)
    {
        self::loadModules();
        $path = Motor::getConf('site')->get('Modules')->path;
        $url = rtrim(Motor::getConf('site')->get('siteUrl'), '/') . '/';

        $ret = '';

        foreach(self::$modules as $moduleName => $module)
        {
            $moduleDir = $url.$path.$moduleName.'/';
            foreach($module->getDependencies('js') as $dependency)
            {
                $ret .= '<script src="'.$moduleDir.$dependency.'" type="text/javascript"></script>';
            }
        }

        return $ret;
    }

    public static function getSlaves($tag)
    {
        return [];
    }
}