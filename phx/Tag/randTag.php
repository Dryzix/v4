<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 14/01/2016
 * Time: 12:33
 */

namespace PHX\Tag;


class randTag implements Tag
{
    private static $min = 0;
    private static $max = 0;

    public static function run($subTag, $content, $options = null)
    {
        $method = 'run'.ucfirst($subTag);
        return self::$method($content);
    }

    public static function runRand($content)
    {
        $nums = explode('|', $content);

        if(count($nums) == 2) {
            if (is_numeric($nums[0]) && is_numeric($nums[1])) {
                return rand($nums[0], $nums[1]);
            }
        }
            return 'INCORRECT PARAMS FOR {rand} TAG';
    }

    public static function runRand_min($min){
       self::$min = $min;
    }

    public static function runRand_max($max){
        self::$max = $max;
    }

    public static function runRand_test(){
        return rand(self::$min, self::$max);
    }

    public static function getSlaves($tag)
    {
        return [];
    }
}