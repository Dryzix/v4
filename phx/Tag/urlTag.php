<?php
/**
 * Class bTag
 *
 * @package fr.phx
 * @license MPL2
 */


namespace PHX\Tag;
use PHX\Core\Motor;
use PHX\Core\Router\Router;

/**
 * Tag {url}
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class urlTag implements Tag{

    /**
     * Méthode générique
     *
     * Lance le tag demandé
     *
     * @param string $subTag Le tag/sous-tag demandé
     * @param string $content Le contenu du tag
     * @param array $options
     * @return string
     */
    public static function run($subTag, $content, $options = null)
    {
        $method = 'run'.ucfirst($subTag);
        return self::$method($content, $options);
    }

    /**
     * Tag {url}
     *
     * Construit une url a partir d'un callable demandé
     *
     * @param string $content
     * @param array $options
     * @return string
     */
    private static function runUrl($content, $options){

        $with = isset($options['with']) ? $options['with'] : (isset($options['w']) ? $options['w'] : []);
        $method = isset($options['method']) ? $options['method'] : (isset($options['m']) ? $options['m'] : 'GET');

        return self::runRoot() . Router::getUrl($content, $method, $with);
    }

    /**
     * Tag {root /}
     *
     * Renvoi l'url racine du site web
     *
     * @return string
     */
    private static function runRoot(){
        return rtrim(Motor::getConf('site')->get('siteUrl'), '/') . '/';
    }

    /**
     * Méthode générique
     *
     * Permet de récupérer une liste des tags dépendants du tag envoyé
     *
     * @param string $tag Le tag pour lequel on souhaite connaitre les dépendences
     * @return array Retourne la liste des tags dépendants
     */
    public static function getSlaves($tag)
    {
       return [];
    }
}