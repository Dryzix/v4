<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 15/01/2016
 * Time: 16:10
 */

namespace PHX\Tag;
use PHX\Core\Motor;

/**
 * Tag {loop}
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class loopTag implements Tag
{

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
     * Éxecuter une boucle
     *
     * Ce tag {loop} permet de répéter son contenu le nombre de fois contenu dans $options['iterations'] ou options['i']
     *
     * @param string $content Contenu entre les balises {loop} et {/loop}
     * @param array $options Paramètres de la balise
     * @return string
     */
    private static function runLoop($content, $options)
    {
        if(isset($options['i']) || isset($options['iterations']))
        {
            $iterations = intval(isset($options['i']) ? $options['i'] : $options['iterations']);
            $return = '';

            for($i = 0; $i<$iterations;$i++)
            {
                $return .= $content;
            }

            return $return;
        }
        else
        {
            return 'PARAMETER FOR TAG LOOP "i" OR "iterations" NOT FOUND';
        }
    }

    /**
     * Méthode générique
     *
     * Permet de récupérer une liste des tags dépendants du tag envoyé
     *
     * @param string$tag Le tag pour lequel on souhaite connaitre les dépendences
     * @return array Retourne la liste des tags dépendants
     */
    public static function getSlaves($tag)
    {
      return [];
    }
}