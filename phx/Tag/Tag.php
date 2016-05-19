<?php
/**
 * Class Tag
 *
 * @package fr.phx
 * @license MPL2
 */

namespace PHX\Tag;

/**
 * Interface Tag
 *
 * Permet d'imposer les méthodes à définir à la création d'un nouveau tag, qui seront utilisés par le Templater
 *
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
interface Tag{
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
    public static function run($subTag, $content, $options = null);

    /**
     * Méthode générique
     *
     * Permet de récupérer une liste des tags dépendants du tag envoyé
     *
     * @param string $tag Le tag pour lequel on souhaite connaitre les dépendences
     * @return array Retourne la liste des tags dépendants
     */
    public static function getSlaves($tag);
}