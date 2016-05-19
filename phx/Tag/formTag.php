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
class formTag implements Tag
{
    /**
     * Méthode générique
     * Permet de récupérer une liste des tags dépendants du tag envoyé
     * @param string $tag Le tag pour lequel on souhaite connaitre les dépendences
     * @return array Retourne la liste des tags dépendants
     */
    public static function getSlaves($tag)
    {
        return [];
    }

    /**
     * Méthode générique
     * Lance le tag demandé
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
     * Créer un formulaire
     *
     * Ce tag {form} permet de générer un formulaire destiné à communiquer avec le moteur Js
     *
     * @param string $content Contenu entre les balises {form} et {/form}
     * @param array $options Paramètres de la balise
     * @return string
     */
    private static function runForm($content, $options)
    {
        if(isset($options['action']) || isset($options['a']))
        {
            $action = isset($options['action']) ? $options['action'] : (isset($options['a']) ? $options['a'] : '');
            $role = isset($options['role']) ? 'role="' . $options['role'] . '"' : '';
            $class = isset($options['class']) ? 'class="' . $options['class'] . '"' : '';
            $token = isset($options['token']) ? $options['token'] : (isset($options['t']) ? $options['t'] : null);

            $add = '';

            if($token !== null)
            {
                $value = Motor::getAuth()->addToken($token);
                $add = '<input type="hidden" name="token_' . $token . '" value="' . $value . '" />';
            }

            $form =
                '<form method="POST" action="' . $action . '" ' . $role . ' ' . $class . ' data-task="1">'
                . $add
                . $content
                . '</form>';

            return $form;
        }
        else
        {
            return 'PARAMETER FOR TAG FORM "action" OR "a" NOT FOUND';
        }
    }
}