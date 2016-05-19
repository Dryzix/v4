<?php
/**
 * Class Router
 *
 * @package fr.phx
 * @license MPL2
 */


namespace PHX\Core\Router;
use PHX\Config\Config;
use PHX\Core\Motor;

/**
 * Gestionnaires de routes
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class Router{
    /**
     * URL Courante
     * @var string
     */
    private static $url;

    /**
     * Tableau des routes
     * @var array
     */
    private static $routes = [];

    /**
     * Trouvé
     * Cette variable contient true si une route a été appelé précedement
     * @var bool
     */
    private static $find = false;

    /**
     * Charge le router
     *
     * Enregistre les différentes routes spécifiés dans le fichier .json de gestion des routes
     *
     * @param string $url URL Courante
     * @param Config $conf La configuration du router
     */
    static function load($url, Config $conf)
    {
        self::$url = $url;

        if($conf->get('GET'))
        {
            foreach((array) $conf->get('GET') AS $url => $route)
            {
                if(isset($route->with))
                {
                    $with = $route->with;
                }
                else
                {
                    $with = [];
                }

                self::add($url, $route->callable, $with, 'GET');
            }
        }

        if($conf->get('POST'))
        {
            foreach((array) $conf->get('POST') AS $url => $route)
            {
                if(isset($route->with))
                {
                    $with = $route->with;
                }
                else
                {
                    $with = [];
                }

                self::add($url, $route->callable, $with, 'POST');
            }
        }
    }

    /**
     * Charge l'url
     *
     * Permet de modifier l'url sur laquelle le routeur doit se baser
     *
     * @param string $url URL
     */
    public static function setUrl($url)
    {
        self::$url = $url;
    }

    /**
     *  Gère l'ajout des routes au routeur
     *
     * @param string $url L'url de la route
     * @param string $callable Le controller et la méthode à appeler
     * @param string $params Les paramètres url
     * @param string $method POST/GET
     * @return Route
     */
    private static function add($url, $callable, $params, $method)
    {
        $route = new Route($url, $callable, $params);
        self::$routes[$method][] = $route;
    }

    /**
     * Récupérer une url
     *
     * Cette méthode permet de retrouver une url correspondant à un callable
     *
     * @param string $callable Le callable a retrouver
     * @param string $method La méthode correspondant à la route (GET/POST)
     * @param array $params Les paramètres dans l'ordre afin de remplir la route
     * @return string
     */
    public static function getUrl($callable, $method = 'GET', $params = [])
    {
        foreach(self::$routes[$method] as $route)
        {
            if($route->matchByCallable($callable, $params))
            {
                return $route->build($params);
            }
        }
        return 'URL NOT FOUND';
    }

    /**
     * hasFind
     *
     * Accesseur de self::$find
     * @return bool
     */
    public static function hasFind(){
        return self::$find;
    }

    /**
     * Lancement du router
     *
     * Cette méthode permet de chercher si l'url courante correspond à une route dans son tableau des routes, si oui, elle l'exécute et retourne le résultat
     *
     * @return string
     * @throws RouterException
     */
    public static function run(){
        if(!isset($_SERVER['REQUEST_METHOD'])){
            throw new RouterException('REQUEST_METHOD does not exist');
        }

        foreach(self::$routes[$_SERVER['REQUEST_METHOD']] as $route)
        {
            if($route->match(self::$url)){
                Router::$find = true;
                return $route->call();
            }
        }
    }

}