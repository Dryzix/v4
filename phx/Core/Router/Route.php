<?php
/**
 * Class Route
 *
 * @package fr.phx
 * @license MPL2
 */


namespace PHX\Core\Router;

use PHX\Core\Motor;

/**
 * Class Route
 *
 * Cette class correspond à une route et permet de la comparer à une url et de lancer, si nécessaire, le contrôleur associé
 *
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class Route{

    /**
     * URL correspondant à la route
     * @var string
     */
    private $url;

    /**
     * Contrôleur à appeler
     * @var string
     */
    private $callable;

    /**
     * Paramètres de l'url
     * @var array
     */
    private $params;

    /**
     * Paramètres trouvés dans l'url correspondante
     * @var array
     */
    private $matches;

    /**
     * Route constructor.
     * @param string $url L'url corrsepondant au callable
     * @param string $callable Le controller et la méthode à appeler si l'url match
     * @param array $params Les paramètres de l'url
     */
    public function __construct($url, $callable, $params)
    {
        $this->url = trim($url, '/');
        $this->callable = $callable;
        $this->params = [];

        foreach ($params as $param => $regex) {
            $param = ltrim($param, ':');
            $this->params[$param] = str_replace('(', '(?:', $regex);
        }
    }

    /**
     * Définition d'un paramètre
     *
     * Cette méthode permet de renvoyer la définition pour un paramètre url si il est fournit dans la configuration du routeur
     *
     * @param string $match
     * @return string
     */
    private function paramMatch($match){
        if(isset($this->params[$match[1]])){
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    /**
     * Comparateur callable
     *
     * Cette méthode renvoi true si le callable passé en paramètre correspond avec la route courante
     *
     * @param string $callable Le callable
     * @return bool Retoune Vrai si le callable passé en paramètre correspond au callable de la route
     */
    public function matchByCallable($callable, $params){
        if($callable == $this->callable)
        {
            $ret = true;
            foreach($this->params as $param => $val)
            {
                if(!isset($params[$param]))
                {
                    $ret = false;
                    break;
                }
            }
            return $ret;
        }
        return false;
    }

    /**
     * Comparateur
     *
     * Permet de vérifier si l'url match et de capturer l'url avec les paramètres get('/posts/:slug-:id') par exemple
     *
     * @param string $url
     * @return bool Retoune Vrai si l'url demandé correspond à l'url de la route
     */
    public function match($url){
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->url);
        $regex = "#^$path$#i";
        if(!preg_match($regex, $url, $matches)){
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    /**
     * Construire une url
     *
     * Cette méthode permet de construire une url a partir des différents paramètres fournis
     *
     * @param array $params Les paramètres afin de remplir l'url
     * @return string
     */
    public function build($params = []){

        $url = $this->url;
        foreach($params AS $param => $value)
        {
            if(isset($this->params[$param]))
            {
                $this->params[$param] = preg_replace('/#/', '\#', $this->params[$param]);
                if(preg_match('#^' . $this->params[$param] . '$#', $value))
                {
                    $url = preg_replace('#:' . preg_quote($param, '#') . '#', $value, $url);
                }
                else
                {
                    $url = preg_replace('#:' . preg_quote($param, '#') . '#', 'INCORRECT PARAM ' . $param . '', $url);
                }
            }
        }
        return $url;
    }

    /**
     * Appel la méthode dans le controleur enregistré pour la route
     *
     * @return mixed
     */
    public function call(){
        $namespace = Motor::getConf('site')->get('Controller')->controllersNamespace;
        Motor::getDebug()->add('callable', $this->callable);
        $namespace = ltrim($namespace, '\\');

        $params = explode('#', $this->callable);
        $controller = '\\' . $namespace . '\\' . ucfirst($params[0]) . "Controller";
        $controller = new $controller();
        return call_user_func_array([$controller, $params[1]], $this->matches);
    }
}