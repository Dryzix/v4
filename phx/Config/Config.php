<?php
/**
 * Class Config
 *
 * @package      fr.phx
 * @license MPL2
 */

namespace PHX\Config;

/**
 * Gestionnaire de configuration
 *
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class Config{
    /**
     * Chemin vers le fichier de conf
     *
     * @var string
     */
    private $filepath;
    /**
     * Type de configuration
     *
     * @var string
     */
    private $json;

    /**
     * Config constructor.
     *
     * @param string $filepath Chermin vers le fichier de conf
     */
    public function __construct($filepath)
    {
        if(file_exists($filepath))
        {
            $this->filepath = $filepath;
        }
        else
        {
            $filepath = null;
        }
    }

    /**
     * Charge une config JSON
     *
     * @return $this Retourne la Config chargé
     */
    public function loadFromJSON(){
        if(!is_null($this->filepath))
        {
            $this->json = json_decode(file_get_contents($this->filepath));
        }
        return $this;
    }

    /**
     * Renvoi un paramètre de la conf
     *
     * @param bool|string $key Le paramètre demandé
     * @return bool|string Renvoi la valeur du paramètre FAUX si inexistant
     */
    public function get($key = false)
    {
        if($key){
            return isset($this->json->$key) ? $this->json->$key : false;
        }else{
            return $this->json;
        }
    }
}