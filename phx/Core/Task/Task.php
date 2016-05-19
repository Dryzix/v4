<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 26/01/2016
 * Time: 10:08
 */

namespace PHX\Core\Task;
use PHX\Core\Motor;

/**
 * Class Task
 * Cette classe est la classe mère des tâches, les tâches sont les processus qui doivent être appeler afin de communiquer avec le client Ajax
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class Task
{
    /**
     * Les instructions à envoyer au client
     * @var array
     */
    protected $instructions;
    /**
     * L'élément HTML sur lequel effectuer les actions
     * @var string
     */
    protected $defaultMessageBox;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->instructions = [];
        Motor::getDebug()->add('task', true);
    }

    /**
     * Supprimer une class HTML
     * @param string $class La class html à supprimer
     * @param string $to A qui la suppprimer
     * @return $this Fluent
     * @throws TaskException
     */
    protected function removeClass($class, $to = '')
    {
        $to = empty($to) ? $this->defaultMessageBox : $to;

        if(!empty($to)) {
            $this->instructions[] =  ['removeTo' => $to, 'class' => $class];
        }else{
            throw new TaskException('EMPTY ARG $TO OR DEFAULT MESSAGE BOX');
        }
        return $this;
    }

    /**
     * Ajouter une class HTML
     * @param string $class La class à ajouter
     * @param string $to A qui l'ajouter
     * @return $this Fluent
     * @throws TaskException
     */
    protected function addClass($class, $to = '')
    {
        $to = empty($to) ? $this->defaultMessageBox : $to;

        if(!empty($to)) {
            $this->instructions[] =  ['addTo' => $to, 'class' => $class];
        }else{
            throw new TaskException('EMPTY ARG $TO OR DEFAULT MESSAGE BOX');
        }
        return $this;
    }

    /**
     * Modifier une class HTML
     * @param string $class La nouvelle classe
     * @param string $to A qui modifier
     * @return $this Fluent
     * @throws TaskException
     */
    protected function setClass($class, $to = '')
    {
        $to = empty($to) ? $this->defaultMessageBox : $to;

        if(!empty($to)) {
            $this->instructions[] =  ['setTo' => $to, 'class' => $class];
        }else{
            throw new TaskException('EMPTY ARG $TO OR DEFAULT MESSAGE BOX');
        }
        return $this;
    }

    /**
     * Ajouter du HTML
     * @param string $html Le HTML à ajouter
     * @param string $to A quelle balise l'ajouter
     * @return $this Fluent
     * @throws TaskException
     */
    protected function setHtml($html, $to = '')
    {
        $to = empty($to) ? $this->defaultMessageBox : $to;

        if(!empty($to)) {
            $this->instructions[] =  ['setTo' => $to, 'html' => $html];
        }else{
            throw new TaskException('EMPTY ARG $TO OR DEFAULT MESSAGE BOX');
        }
        return $this;
    }

    /**
     * Ajouter du texte
     * @param string $text Le texte à ajouter
     * @param string $to A quelle balise l'ajouter
     * @return $this Fluent
     * @throws TaskException
     */
    protected function setText($text, $to = '')
    {
        $to = empty($to) ? $this->defaultMessageBox : $to;

        if(!empty($to)) {
            $this->instructions[] =  ['setTo' => $to, 'text' => $text];
        }else{
            throw new TaskException('EMPTY ARG $TO OR DEFAULT MESSAGE BOX');
        }
        return $this;
    }

    /**
     * Ajouer un callback
     *
     * Cette méthode permet de forcer l'appel à une autre fonction Javascript à la lecture de la réponse serveur
     * @param string $callback La fonction à appeler
     * @param string $args Les arguments que peut prendre la fonction
     * @return $this Fluent
     */
    protected function addCallback($callback, $args = '')
    {
        $this->instructions[] = ['callback' => $callback, 'args' => $args];
        return $this;
    }

    /**
     * Fondre un élément
     *
     * Cette méthode permet de cacher un élément en fonction de son sélécteur
     * @param string $selector L'élément
     * @param int $timing Le temps en millisecondes que doit mettre l'élément à fondre
     * @return $this Fluent
     */
    public function fadeOut($selector = '', $timing = 500){
        $selector = empty($selector) ? $this->defaultMessageBox : $selector;
        $this->instructions[] = ['fadeOut' => $selector, 'timing' => $timing];
        return $this;
    }

    /**
     * Fondu d'apparition
     *
     * Cette méthode permet de faire apparaitre un élément en fonction de son sélécteur
     * @param string $selector L'élément
     * @param int $timing Le temps en millisecondes que doit mettre l'élément à apparaitre
     * @return $this Fluent
     */
    public function fadeIn($selector = '', $timing = 500){
        $selector = empty($selector) ? $this->defaultMessageBox : $selector;
        $this->instructions[] = ['fadeIn' => $selector, 'timing' => $timing];
        return $this;
    }

    /**
     * Fondre un élément
     *
     * Cette méthode permet de cacher un élément en fonction de son sélécteur dans $in millisecondes
     * @param string $selector L'élément
     * @param int $in Le nombre de millisecondes avant le déclanchement
     * @param int $timing Le temps en millisecondes que doit mettre l'élément à fondre
     * @return $this Fluent
     */
    public function fadeOutIn($selector = '', $in = 500, $timing = 500){
        $selector = empty($selector) ? $this->defaultMessageBox : $selector;
        $this->instructions[] = ['fadeOutIn' => $selector, 'in' => $in, 'timing' => $timing];
        return $this;
    }

    /**
     * Fondu d'apparition
     *
     * Cette méthode permet de faire apparaitre un élément en fonction de son sélécteur dans $in millisecondes
     * @param string $selector L'élément
     * @param int $in Le nombre de millisecondes avant le déclanchement
     * @param int $timing Le temps en millisecondes que doit mettre l'élément à apparaitre
     * @return $this Fluent
     */
    public function fadeInIn($selector = '', $in = 500, $timing = 500){
        $selector = empty($selector) ? $this->defaultMessageBox : $selector;
        $this->instructions[] = ['fadeInIn' => $selector, 'in' => $in, 'timing' => $timing];
        return $this;
    }

    /**
     * Ajouer une redirection
     *
     * Cette méthode permet de forcer une redirection Javascript à la lecture de la réponse serveur
     * @param string $url L'url vers laquelle rediriger
     * @param int $timing Le nombre de microsecondes avant la redirection
     * @return $this Fluent
     */
    protected function redirect($url, $timing = 0)
    {
        $this->instructions[] = ['redirect' => $url, 'timing' => $timing];
        return $this;
    }

    /**
     * Modifier une url
     *
     * Cette méthode permet de modifier l'url affiché par l'utilisateur
     * @param string $url L'url à afficher
     * @param string $title Le titre a afficher
     * @return $this Fluent
     */
    protected function changeUrl($url, $title = '')
    {
        $this->instructions[] = ['changeUrl' => $url, 'title' => $title];
        return $this;
    }


     /**
     * Vider le formulaire
     *
     * Cette méthode permet de vider les champs du formulaire à la lecture de la réponse serveur
     * @return $this Fluent
     */
    protected function emptyForm()
    {
        $this->instructions[] = ['emptyForm' => true];
        return $this;
    }

    /**
     * Construction de la requête
     *
     * Cette méthod encode en JSON la réponse à envoyer au client Javascript
     * @return string
     */
    protected function build(){
        return json_encode($this->instructions);
    }

    /**
     * Permet de déclencher un .fail() en ajax
     */
    public function error()
    {
        header("HTTP/1.1 500 ERROR", true, 200);
    }

    /**
     * Méthode générique
     *
     * Cette méthode est la méthode a ré-écrire pour chaque task
     * @return string
     */
    protected function run()
    {
        return '';
    }
}