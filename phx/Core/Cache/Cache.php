<?php

namespace PHX\Core\Cache;

use PHX\Core\Motor;

class Cache{
    private $cachePath;
    private $root;

    public function __construct($cachePath)
    {
        $this->cachePath = $cachePath;
        $this->root = Motor::getConf('siteConf', Motor::getConf()->get('siteConf'))->get('rootPath');;
    }

    /**
     * Retourne vrai si le cache est à jour en fonction de l'intervalle indiqué en secondes
     *
     * @param string $filepath
     * @param int $intervalle
     * @return bool
     */
    public function isUpdated($filepath, $intervalle = 0)
    {
        if(file_exists($this->root.$this->cachePath.md5($filepath).'.tmp')) {
            if (filemtime($this->root . $this->cachePath . md5($filepath) . '.tmp') >= filemtime($filepath)) {
                return true;
            }
            else
            {
                return false;
            }
        }

        return false;
    }

    public function get($filepath)
    {
        if(file_exists($filepath))
        {
            return file_get_contents($this->root.$this->cachePath.md5($filepath).'.tmp');
        }
        else
        {
            return null;
        }
    }

    public function set($filepath, $content, $parents = [])
    {
        $dependencies = json_encode($parents);
        $fp = fopen($this->root.$this->cachePath.md5($filepath).'.dependencies.tmp', "w");
        fwrite($fp, $dependencies);
        fclose($fp);
        $fp = fopen($this->root.$this->cachePath.md5($filepath).'.tmp', "w");
        fwrite($fp, $content);
        fclose($fp);
    }
}