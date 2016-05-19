<?php
/**
 * Class bTag
 *
 * @package fr.phx
 * @license MPL2
 */


namespace PHX\Tag;

/**
 * Tag {date}
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class dateTag implements Tag{

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
        return self::$method($content, $options);
    }

    /**
     * Tag {date}
     *
     * Retourne une date au format demandé
     *
     * @param string $content
     * @return string
     */
    private static function runDate($content, $options){
        if(isset($options['format']) || isset($options['f']))
        {
            $format = isset($options['format']) ? $options['format'] : $options['f'];
            $date = new \DateTime($content);
            return $date->format($format);
        }
        else
        {
            return 'INCORRECT PARAMS FOR {date} TAG : MISSING format (or f)';
        }
    }

    public static function getSlaves($tag)
    {
       return [];
    }
}