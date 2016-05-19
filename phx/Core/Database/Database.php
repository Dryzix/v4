<?php
/**
 * Interface Database
 *
 * @package fr.phx
 * @license MPL2
 */

namespace PHX\Core\Database;

/**
 * Interface Database
 *
 * Cette interface permet de régir les différentes méthodes qui doivent être utilisés par TOUT le moteur peu importe le type de base de donnée souhaité
 *
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
interface Database{

    /**
     * Base de données - select
     *
     * Selection des colonnes à choisir dans la base de données
     *
     * @param string $columns Les colonnes souhaitées
     * @return $this Fluent
     */
    public function select($columns);

    /**
     * Base de données - insert
     *
     * Selection de la table dans laquelle insérer dans la base de données
     *
     * @param string $table La table souhaitée
     * @return $this Fluent
     */
    public function insert($table);

    /**
     * Base de données - into
     *
     * Selection des champs de la table dans laquelle on souhaite insérer dans la base de données
     *
     * @param string $columns Les colonnes que l'on souhaite insérer
     * @return $this Fluent
     */
    public function into($columns);

    /**
     * Base de données - update
     * Selection de la table dans laquelle faire une mise à jour dans la base de données
     * @param string $table La table souhaitée
     * @return $this Fluent
     */
    public function update($table);

    /**
     * Base de données - set
     * Selection des champs sur lesquels faire la mise à jour dans la base de données
     * @param string $set Les champs à mettre à jour
     * @return $this Fluent
     */
    public function set($set);

    /**
     * Base de données - delete
     *
     * Selection de la table dans laquelle supprmimer des enregistrements dans la base de données
     *
     * @param string $table La table souhaitée
     * @return $this Fluent
     */
    public function delete($table);

    /**
     * Base de données - from
     *
     * Selection des tables à choisir dans la base de donnée
     *
     * @param array $tables Les tables souhaitées
     * @return $this Fluent
     */
    public function from($tables);

    /**
     * Base de données - join
     *
     * Permet de joindre deux tables dans la base de données
     *
     * @param string $from depuis quelle table on souhaite faire la jointure
     * @param array $to Table cible de la joiture au format [Alias => Table] ou [Table]
     * @param string $on La condition de jointure
     * @param string $type Le type de jointure (Ex : LEFT, INNER, FULL, RIGHT)
     * @return $this Fluent
     */
    public function join($from, $to, $on, $type);

    /**
     * Base de données - where
     *
     * Setter des conditions de la requête
     *
     * @param string $conditions Les conditions dans le WHERE
     * @return $this Fluent
     */
    public function where($conditions);

    /**
     * Base de données - groupBy
     *
     * Setter des groupements de la requête
     *
     * @param string $group Les valeurs dans le GROUP BY
     * @return $this Fluent
     */
    public function groupBy($group);

    /**
     * Base de données - having
     *
     * Setter des groupements de la requête
     *
     * @param string $having Les valeurs dans le HAVING
     * @return $this Fluent
     */
    public function having($having);

    /**
     * Base de données - orderBy
     *
     * Choix des ordre dans la requête
     *
     * @param string $order Les valeurs dans le ORDER BY
     * @return $this Fluent
     */
    public function orderBy($order);

    /**
     * Base de données - limit
     *
     * Choix de la limite de la requête
     *
     * @param string $limit Les valeurs dans le LIMIT
     * @return $this Fluent
     */
    public function limit($limit);

    /**
     * Base de données - setValues
     *
     * Permet d'envoyer les valeurs à injecter dans la requête
     *
     * @param array $values Les valeurs à injecter si nécessaire
     * @return $this Fluent
     */
    public function setValues($values);

    /**
     * Base de données - execute
     *
     * Préparation de la requête et execution, sans parcourir les résultats
     *
     * @param string $key La clef afin de pouvoir retrouver la requête
     * @return bool Retourne vrai si l'éxecution s'est fait sans erreur, faux sinon
     */
    public function execute($key = 'default');

    /**
     * Base de données - next
     *
     * Boucle sur une requête select
     *
     * @param string $key La clef afin de pouvoir retrouver la requête
     * @param bool $destroy Vrai si il faut détruire la requête, faux sinon
     * @return array|bool Retourne un tableau correspondant à la ligne correspondante si il reste des résultats, faux sinon
     */
    public function next($key = 'default', $destroy = true);

    /**
     * Base de données - destroy
     *
     * Détruire une requête
     *
     * @param string $key La clef afin de pouvoir retrouver la requête
     * @return array|bool Retourne un tableau correspondant à la ligne correspondante si il reste des résultats, faux sinon
     */
    public function destroy($key = 'default');

    /**
     * Base de données - describe
     *
     * Donne les champs de la table
     *
     * @param string $table La table pour laquelle on souhaite récupérer les champs
     * @return array Les champs de la table
     */
    public function describe($table);

    /**
     * Base de données - count
     *
     * Cette méthode permet de compter le nombre de résultats du précédent execute()
     * @param string $key
     * @param bool $withoutLimit
     * @return bool|int
     */
    public function count($key = 'default', $withoutLimit = false);

    /**
     * Base de données - exist
     *
     * Vérifie si la requête $key éxiste bien
     *
     * @param string $key La clef de la requête à vérifier
     * @return bool Retourne vrai si la requête existe, faux sinon
     */
    public function exist($key);

    /**
     *  Base de données - toString
     *
     * Permet de convertir la requête construite en chaine de caractères
     * @param bool $withValues Si il est nécessaire de convertir les valeurs
     * @return string La requête SQL générée
     */
    public function toString($withValues = false);

}