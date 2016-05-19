<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 22/01/2016
 * Time: 15:48
 */

namespace PHX\Core\Debug;


use PHX\Core\Motor;

class Debug
{
    private $file;
    private $datas;
    private $time_start;

    public function __construct($url)
    {
        $this->datas = [];
        $rootPath = Motor::getConf('site')->get('rootPath');
        $debugPath = $rootPath . Motor::getConf('site')->get('Debug')->path;

        if(!is_dir($debugPath))
        {
            if(!mkdir($debugPath, '0777'))
            {
                throw new DebugException('CANNOT CREATE "' . $debugPath . '" DIR');
            }
        }

        $this->file  = $debugPath . (empty($url) ? 'index.php' : $url) . '.json';
        $file = preg_replace('#' . preg_quote($debugPath, '#') . '#', '', $this->file) ;
        $fileChanged = preg_replace('#/#', '[[_]]', $file);
        $this->file = preg_replace('#' . preg_quote($file, '#') . '#', $fileChanged, $this->file);

        if(!fopen($this->file, "w"))
        {
            throw new DebugException('CANNOT CREATE "'. $this->file .'" FILE');
        }

        $motor = [];
        $motor['debugPath'] = Motor::getConf('site')->get('siteUrl') . Motor::getConf('site')->get('Debug')->path;

        $fp = fopen($debugPath . '__debug.json', "w");
        fwrite($fp, json_encode($motor));
        fclose($fp);

        $debug = Motor::getConf()->get('motorPath') . 'ressources/';

        if(!file_exists(realpath($debugPath . 'index.php')))
        {
            $this->recurse_copy($debug, realpath($debugPath . '..'));
        }

        $this->time_start = microtime(true);

    }

    public function add($key, $values)
    {

        if(preg_match('#\[\]$#', $key)){
            $key = preg_replace('#(.+)\[\]#', '\1', $key);
            if(!isset($this->datas[$key])) {
                $this->datas[$key] = [];
            }
            $this->datas[$key][] = $values;
        }else{
            $this->datas[$key] = $values;
        }

    }

    public function terminate(){
        $time_end = microtime(true);
        $execution_time = round(($time_end - $this->time_start),5);
        $this->add('time', $execution_time);

        $fp = fopen($this->file, "w");
        fwrite($fp, json_encode($this->datas));
        fclose($fp);

    }

    private function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}