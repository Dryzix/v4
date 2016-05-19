<?php
/**
 * Class ifTag
 *
 * @package fr.phx
 * @license MPL2
 */

namespace PHX\Tag;
use PHX\Core\Motor;

/**
 * Tags {if/elseif/else}
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class ifTag implements Tag
{
    /**
     * Tableau de boole
     * @var array
     */
    private static $boole = [];

    /**
     * Résultat du dernier If/Elseif
     * @var bool
     */
    private static $lastIf;

    /**
     * Méthode run - If
     *
     * Lance le tag demandé et instancie le tableau d'algèbre de boole
     *
     * @param string $subTag Le tag/sous-tag demandé
     * @param string $content Le contenu du tag
     * @param array $options
     * @return string
     */
    public static function run($subTag, $content, $options = null)
    {
        self::$boole['0and0'] = false;
        self::$boole['0and1'] = false;
        self::$boole['1and0'] = false;
        self::$boole['1and1'] = true;
        self::$boole['0or0'] = false;
        self::$boole['1or0'] = true;
        self::$boole['0or1'] = true;
        self::$boole['1or1'] = true;
        self::$boole['1'] = true;
        self::$boole['0'] = false;

        $method = 'run'.ucfirst($subTag);
        return self::$method($content, $options);
    }

    /**
     * Tag IF
     *
     * Permet d'éxecuter une condition
     *
     * @param string $content Contenu entre les balises {if}{/if}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function runIf($content, $options){

        if(isset($options['ccond']))
        {
            $bool = self::ccond($options['ccond']);
            if(isset(self::$boole[$bool]))
            {
                if(self::$boole[$bool])
                {
                    self::$lastIf = true;
                    return $content;
                }
                else
                {
                    self::$lastIf = false;
                    return '';
                }
            }
            else
            {
                return "YOUR COMPLEXE CONDITION IS NOT VALID";
            }

        }
        else {

            // Si la condition est présente ainsi que ses paramètres
            if (isset($options['cond']) && (isset($options['param']) || isset($options['params']))) {
                $method = 'if' . ucfirst(strtolower($options['cond']));
                if (method_exists(__CLASS__, $method)) {
                    return self::$method($content, $options);
                } else {
                    return 'CONDITION "' . $options['cond'] . '" DOES NOT EXIST';
                }
            } else {
                return 'PARAMETER FOR TAG IF "cond" AND ("param" OR "params") NOT FOUND';
            }
        }
    }

    /**
     * Tag ELSEIF
     *
     * Permet d'éxecuter une condition
     *
     * @param string $content Contenu entre les balises {elseif}{/elseif}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function runElseif($content, $options){
        if(is_null(self::$lastIf))
        {
            return 'PREVIOUS TAG IF DOES NOT EXIST';
        }
        else {

            if(!self::$lastIf) {
                if (isset($options['ccond'])) {
                    $bool = self::ccond($options['ccond']);
                    if (isset(self::$boole[$bool])) {
                        if (self::$boole[$bool]) {
                            self::$lastIf = true;
                            return $content;
                        } else {
                            self::$lastIf = false;
                            return '';
                        }
                    } else {
                        return "YOUR COMPLEXE CONDITION IS NOT VALID";
                    }

                } else {

                    // Si la condition est présente ainsi que ses paramètres
                    if (isset($options['cond']) && (isset($options['param']) || isset($options['params']))) {
                        $method = 'if' . ucfirst(strtolower($options['cond']));
                        if (method_exists(__CLASS__, $method)) {
                            return self::$method($content, $options);
                        } else {
                            return 'CONDITION "' . $options['cond'] . '" DOES NOT EXIST';
                        }
                    } else {
                        return 'PARAMETER FOR TAG IF "cond" AND ("param" OR "params") NOT FOUND';
                    }
                }
            }
            else
            {
                return '';
            }
        }
    }

    /**
     * Tag ELSE
     *
     * Permet d'afficher le contenu si la condition précédente est fausse
     *
     * @param string $content Contenu entre les balises {else}{/else}
     * @return string
     */
    private static function runElse($content)
    {
        if(is_null(self::$lastIf))
        {
            return 'PREVIOUS TAG IF DOES NOT EXIST';
        }
        else
        {
            if(!self::$lastIf)
            {
                self::$lastIf = null;
                return $content;
            }
            else
            {
                self::$lastIf = null;
                return '';
            }
        }
    }

    /**
     * Conditions complexes
     *
     * Permet de résoudre une série de conditions complexes avec gestion de parenthèses
     *
     * @param string $conditions La condition complexe
     * @return string
     */
    private static function ccond($conditions)
    {
        $return = '0';
        preg_match_all('#([\w]*\((?:([^\(\)]*)|(?:(?2)(?1)(?2))*)\))#', $conditions, $out);

        foreach($out[1] as $cond)
        {
            $cond = preg_replace('#^\(#', '', $cond, -1, $count);

            if($count == 1)
            {
                $cond = preg_replace('#\)$#', '', $cond);
            }

            if(preg_match('#^((?:[\w]+)\([^\)]*?\))$#', $cond))
            {
                $regex = '#' . preg_quote($cond, '#') . '#';
                $conditions = preg_replace($regex, self::execCond($cond), $conditions);
            }
            elseif(preg_match('#(\((?:([^\(\)]*)|(?:(?2)(?1)(?2))*)\))#', $cond))
            {
                $regex = '#' . preg_quote($cond, '#') . '#';
                $conditions = preg_replace($regex, self::ccond($cond), $conditions);
            }
        }
        $conditions = preg_replace('#\s#', '', $conditions);
        $conditions = preg_replace('#\(#', '', $conditions);
        $conditions = preg_replace('#\)#', '', $conditions);


        if(isset(self::$boole[$conditions]))
        {
            $conditions = self::$boole[$conditions] ? '1' : '0';
        }

        return $conditions;
    }

    /**
     * Executer une condition
     *
     * Permet a la méthode ccond() d'éxcuter les différentes conditions passé en paramètre
     *
     * @param string $cond La condition à effectuer
     * @return string 1 si la contition a fonctionné, 0 sinon
     */
    private static function execCond($cond)
    {
        if(preg_match('#^([\w]+)\(([^\)]*)\)$#', $cond, $out)){
            $method = 'if' . ucfirst($out[1]);
            $val = $out[2];

            if (method_exists(__CLASS__, $method)) {
                $content = 'bidon';
                return self::$method($content, ['param' => $val]) == '' ? '0' : '1';

            }else{
                return '0';
            }
        }else{
            return '0';
        }
    }

    /**
     * Tag if - cond=equal
     *
     * Affiche le contenu du if si la variable passé en égal au second paramètre
     *
     * @param string $content Contenu entre les balises {if}{/if}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function ifEqual(&$content, $options)
    {

        preg_match('#(.+),(?:\'|")(.+)(?:\'|")#is', $options['param'], $out);

        if(count($out) > 2)
        {
            if($out[1] == $out[2])
            {
                self::$lastIf = true;
                return $content;
            }
            else
            {
                self::$lastIf = false;
                return '';
            }
        }
        else
        {
            self::$lastIf = false;
            return '';
//            return 'INVALID PARAM FOR TAG IF - equal';
//            A VOIR AVEC LE DEBUGGER
        }
    }

    /**
     * Tag if - cond=isset
     *
     * Affiche le contenu du if si la variable passé en paramètre existe
     *
     * @param string $content Contenu entre les balises {if}{/if}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function ifIsset(&$content, $options)
    {
        if(!preg_match('@&#x7B;&#x7B;([\w]+)&#x7D;&#x7D;@', $options['param'], $out))
        {
            self::$lastIf = true;
            return $content;
        }
        else
        {
            if(Motor::getTemplater()->issetVar($out[1]))
            {
                self::$lastIf = true;
                return $content;
            }
        }
        self::$lastIf = false;
        return '';
    }


    /**
     * Tag if - cond=empty
     *
     * Affiche le contenu du if si la variable passé en paramètre est vide
     *
     * @param string $content Contenu entre les balises {if}{/if}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function ifEmpty(&$content, $options)
    {
        if(empty($options['param']))
        {
            self::$lastIf = true;
            return $content;
        }
        self::$lastIf = false;
        return '';
    }

    /**
     * Tag if - cond=notEmpty
     *
     * Affiche le contenu du if si la variable passé en paramètre n'est pas vide
     *
     * @param string $content Contenu entre les balises {if}{/if}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function ifNotEmpty($content, $options)
    {
        if(!empty($options['param']))
        {
            self::$lastIf = true;
            return $content;
        }
        self::$lastIf = false;
        return '';
    }

    /**
     * Tag if - cond=isTrue
     *
     * Affiche le contenu du if si la variable passé en paramètre est true
     *
     * @param string $content Contenu entre les balises {if}{/if}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function ifIsTrue(&$content, $options)
    {
        if((strtolower($options['param']) === 'true') || $options['param'] === '1')
        {
            self::$lastIf = true;
            return $content;
        }

        self::$lastIf = false;
        return '';
    }

    /**
     * Tag if - cond=isFalse
     *
     * Affiche le contenu du if si la variable passé en paramètre est false
     *
     * @param string $content Contenu entre les balises {if}{/if}
     * @param array $options Paramètres du tag
     * @return string
     */
    private static function ifIsFalse(&$content, $options)
    {
        if((strtolower($options['param']) === 'false') || $options['param'] === '0')
        {
            self::$lastIf = true;
            return $content;
        }
        self::$lastIf = false;
        return '';
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