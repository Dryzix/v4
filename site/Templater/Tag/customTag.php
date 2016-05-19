<?php
namespace SITE\Templater\Tag;

use PHX\Tag\Le;
use PHX\Tag\Tag;

class customTag implements Tag{

    public static function run($subTag, $content, $options = null)
    {
        $method = 'run'.ucfirst($subTag);
        return self::$method($content, $options);
    }

    private static function runPerso($content, $options){
        return '<div style="width:400px;height:300px;background:grey;">' . $content . '</div>';
    }

    /**
     * Récupérer les esclaves
     *
     * Permet de récupérer une liste des tags dépendants du tag envoyé
     *
     * @param $tag Le tag pour lequel on souhaite connaitre les dépendences
     * @return array Retourne la liste des tags dépendants
     */
    public static function getSlaves($tag)
    {
        return [];
    }
}