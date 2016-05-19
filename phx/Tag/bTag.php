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
class bTag implements Tag{

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
     * Tag {b}
     *
     * Retourne le texte en gras
     *
     * @param string $content
     * @return string
     */
    private static function runB($content){
        return '<span style="font-weight: bold">' . $content . '</span>';
    }

    public static function getSlaves($tag)
    {
       return [];
    }
}