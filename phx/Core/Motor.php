<?php
/**
 * Class Motor
 *
 * @package      fr.phx
 * @license MPL2
 */


namespace PHX\Core;

use PHX\Config\Config;
use PHX\Core\Auth\Auth;
use PHX\Core\Database\Database;
use PHX\Core\Database\MysqlDatabase;
use PHX\Core\Debug\Debug;
use PHX\Core\Router\Router;
use PHX\Core\Templater\Templater;


/**
 * Moteur du framework
 *
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class Motor{
    /**
     * Liste des configurations
     *
     * @var array
     */
    private static $configs = [];

    /**
     * Singleton du templater
     *
     * @var Templater
     */
    private static $templater;

    /**
     * Singleton de la base de données
     *
     * @var Database
     */
    private static $db;

    /**
     * Singleton du debugger
     *
     * @var Debug
     */
    private static $debug;

    /**
     * Singleton de la session
     *
     * @var Auth
     */
    private static $auth;


    /**
     * Accesseur de configuration
     *
     * Permet de récupérer une configuration
     *
     * @param string $key La clé pour récupérer la configuration
     * @param string $filepath Le chemin vers le fichier de configuration
     * @param string $filetype Le type du fichier de configuration
     * @return Config Une instance de la Config demandé
     */
    public static function getConf($key = 'main', $filepath = 'MOTOR', $filetype = 'json'){
        if($filepath == 'MOTOR'){
            $filepath = realpath(__DIR__. '/../conf/motor.json');
        }

        if(!isset(self::$configs[$key]))
        {
            $config =  new Config($filepath);

            switch($filetype)
            {
                case 'json':
                    self::$configs[$key] = $config->loadFromJSON();
                    break;
            }
        }
        return self::$configs[$key];
    }

    /**
     * Retourne le Singleton du Templater
     *
     * @return Templater
     */
    public static function getTemplater(){
        if(self::$templater == null){
            self::$templater = new Templater();
        }
        return self::$templater;
    }

    /**
     * Singleton de la base de donnée
     *
     * @return Database
     */
    public static function getDb(){
        if(self::$db == null){
            $dbUser = Motor::getConf('site')->get('Database')->dbUser;
            $dbPassword = Motor::getConf('site')->get('Database')->dbPassword;
            $dbName = Motor::getConf('site')->get('Database')->dbName;
            $dbHost = Motor::getConf('site')->get('Database')->dbHost;
            self::$db = new MysqlDatabase($dbHost, $dbName, $dbUser, $dbPassword);
        }
        return self::$db;
    }

    /**
     * Singleton de la session utilisateur
     *
     * @return Auth
     */
    public static function getAuth(){
        if(self::$auth == null){
            self::$auth = new Auth();
        }
        return self::$auth;
    }

    /**
     * Singleton Debug
     * @param string $url Paramètre contenant l'url de la page courante, paramètre obligatoire uniquement au premier appel
     * @return Debug
     */
    public static function getDebug($url = '')
    {
        if(self::$debug == null){
            self::$debug = new Debug($url);
        }
        return self::$debug;
    }

    /**
     * Lancement du moteur
     *
     * Cette méthode se charge de trouver la vue à charger grâce aux configurations du Router et charge lance le Templater
     *
     * @param string $confSitePath Le chemin vers le fichier .json de configuration du site client
     * @return string Retourne le HTML à afficher
     * @throws \PHX\Core\Router\RouterException
     */
    public static function run($confSitePath){
        Motor::getAuth(); // Lancement de la session
        $rootPath = self::getConf('site', $confSitePath)->get("rootPath");
        $confPath = self::getConf('site')->get('confPath');
        $routesConfFile = self::getConf('site')->get('Router')->routesConfFile;

        try {

            Motor::getDebug($_GET['url']);

            Router::load($_GET['url'], self::getConf('router', $rootPath . $confPath . $routesConfFile));

            $page = Router::run();

            if (!Router::hasFind()) {
                Router::setUrl(self::getConf('site')->get('ErrorPages')->error404);
                header("HTTP/1.1 404 ERROR", true, 404);
                $page = Router::run();
            }

            Motor::getDebug()->terminate();
        }
        catch(\Exception $e)
        {
            preg_match('#.*\\\\([^\\\\]+)#', get_class($e), $class);
            return "<b>" . $class[1] . " Found : </b>" . $e->getMessage();
        }

        return $page;
    }
}