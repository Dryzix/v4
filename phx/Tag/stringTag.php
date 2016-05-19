<?php
/**
 * Class bTag
 *
 * @package fr.phx
 * @license MPL2
 */


namespace PHX\Tag;

/**
 * Tag {b}
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class stringTag implements Tag{

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
        return self::$method($content);
    }

    /**
     * Tag {txt}
     *
     * Retourne le texte modifié avec htmlentities
     *
     * @param string $content
     * @return string
     */
    private static function runTxt($content){
        return htmlentities($content);
    }

    public static function getSlaves($tag)
    {
       return [];
    }
}