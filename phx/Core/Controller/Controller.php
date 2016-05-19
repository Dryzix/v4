<?php
/**
 * Class Controller
 *
 * @package fr.phx
 * @license MPL2
 */


namespace PHX\Core\Controller;
use PHX\Core\Motor;

/**
 * Gestionnaires de vues
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class Controller
{
    /**
     * Le template dans lequel on souhaite insérer la vue
     * @var string
     */
    protected $tpl;

    /**
     * Rendenring
     * Cette méthode permet d'insérer la vue dans le template souhaité
     * @param string $view Le .tpl de la vue
     * @return string Le HTML à afficher
     */
    protected function render($view){
        $render = Motor::getConf('site')->get('rootPath') . $this->tpl;
        $render = Motor::getTemplater()->loadTemplate($render);
        $view = Motor::getConf('site')->get('rootPath') . $view;
        $view = Motor::getTemplater()->loadTemplate($view);
        $render = preg_replace('#{VIEW}#', $view, $render, -1, $count);

        return $render;
    }
}