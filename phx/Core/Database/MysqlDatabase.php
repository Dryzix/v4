<?php
/**
 * Class MysqlDatabase
 *
 * @package fr.phx
 * @license MPL2
 */

namespace PHX\Core\Database;
use PDO;
use PHX\Core\Motor;


/**
 * Class MysqlDatabase
 *
 * Cette classe contient la définition de toutes les méthodes de l'interface Database afin de permettre au moteur d'utiliser une base de donnée de type Mysql
 *
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class MysqlDatabase implements Database{

    /**
     * Pour Mysql : PDO
     * @var \PDO
     */
    private $pdo;

    /**
     * Variable contenant les requêtes
     * @var string
     */
    private $requests;

    /**
     * Colonnes choisis
     * @var string
     */
    private $columns;

    /**
     * Tables choisis
     * @var array
     */
    private $tables;

    /**
     * Type de requête
     * @var string
     */
    private $typeOfRequest;

    /**
     * Liste des jointures
     * @var array
     */
    private $joins;

    /**
     * Condition (where)
     * @var string
     */
    private $conditions;

    /**
     * Contenu du GROUP BY
     * @var string
     */
    private $group;

    /**
     * Contenu du HAVING
     * @var string
     */
    private $having;

    /**
     * Contenu du ORDER BY
     * @var string
     */
    private $order;

    /**
     * Contenu du LIMIT
     * @var string
     */
    private $limit;

    /**
     * Table pour le INSERT INTO
     * @var string
     */
    private $insert;

    /**
     * Colonnes pour le INSERT INTO
     * @var string
     */
    private $into;

    /**
     * Colonnes pour le UPDATE
     * @var string
     */
    private $update;

    /**
     * Colonnes pour le SET de UPDATE
     * @var string
     */
    private $set;

    /**
     * Colonnes pour DELETE
     * @var string
     */
    private $delete;

    /**
     * Valeurs du update pour le execute()
     * @var array
     */
    private $updateValues;

    /**
     * Valeurs pour le execute()
     * @var array
     */
    private $values;

    /**
     * Nombre de résultats de la requête
     * @var int
     */
    private $count;

    /**
     * Nombre de résultats de la requête sans le LIMIT
     * @var int
     */
    private $countWithoutLimit;

    /**
     * MysqlDatabase constructor.
     * @param string $dbHost L'hôte de la base de données
     * @param string $dbName Le nom de la base de données
     * @param string $dbUser L'utilisateur de la base de données
     * @param string $dbPassword Le mot de passe de l'utilisateur de la base de données
     * @throws DatabaseException
     */
    public function __construct($dbHost, $dbName, $dbUser, $dbPassword)
    {

        try{
            $this->pdo = new \PDO('mysql:dbname=' . $dbName . ';host=' . $dbHost, $dbUser, $dbPassword);
            $this->pdo->exec('SET NAMES "utf8"');
        }catch(\Exception $e){
            throw new DatabaseException('MYSQL CONNEXION FAILURE');
        }


        $this->requests = [];
        $this->columns = '';
        $this->tables = [];
        $this->joins = [];
        $this->conditions = '';
        $this->group = '';
        $this->having = '';
        $this->order = '';
        $this->limit = '';
        $this->values = [];
        $this->insert = '';
        $this->into = '';
        $this->update = '';
        $this->set = '';
        $this->delete = '';
        $this->updateValues = [];
        $this->count = null;
        $this->countWithoutLimit = null;
    }

    /**
     * Base de données Mysql - insert
     * Selection de la table dans laquelle insérer dans la base de données
     * @param string $table La table souhaitée
     * @return $this Fluent
     */
    public function insert($table)
    {
        $this->typeOfRequest = 'insert';
        $this->insert = $table;
        return $this;
    }

    /**
     * Base de données Mysql - into
     * Selection des champs de la table dans laquelle on souhaite insérer dans la base de données
     * @param string $columns Les colonnes que l'on souhaite insérer
     * @return $this Fluent
     */
    public function into($columns = '')
    {
        if($columns != '')
        {
            $columns = trim($columns, '()');
            $columns = '(' . $columns . ')';
        }
        $this->into = $columns;
        return $this;
    }

    /**
     * Base de données Mysql - update
     * Selection de la table dans laquelle faire une mise à jour dans la base de données
     * @param string $table La table souhaitée
     * @return $this Fluent
     */
    public function update($table)
    {
        $this->typeOfRequest = 'update';
        $this->update = $table;
        return $this;
    }

    /**
     * Base de données Mysql - set
     * Selection des champs sur lesquels faire la mise à jour dans la base de données
     * @param string $set Les champs à mettre à jour
     * @return $this Fluent
     */
    public function set($set)
    {
        $this->set = $set;
        return $this;
    }

    /**
     * Base de données Mysql - delete
     * Selection de la table dans laquelle supprmimer des enregistrements dans la base de données
     * @param string $table La table souhaitée
     * @return $this Fluent
     */
    public function delete($table)
    {
        $this->typeOfRequest = 'delete';
        $this->delete = $table;
        return $this;
    }

    /**
     * Base de données Mysql - select
     * Choix des colonnes à afficher sans une requête SELECT
     * @param string $columns
     * @return $this
     */
    public function select($columns = '*')
    {
        $this->typeOfRequest = 'select';
        $this->columns = $columns;
        return $this;
    }

    /**
     * Base de données Mysql - from
     *
     * Selection des tables à choisir dans la base de donnée
     *
     * @param array $tables Les tables souhaitées au format 'ALIAS' => 'TABLE'
     * @return $this
     */
    public function from($tables)
    {
        if(is_string($tables))
        {
            $tables = [ $tables ];
        }
        $this->tables = $tables;
        return $this;
    }

    /**
     * Base de données Mysql - join
     *
     * Permet de joindre deux tables dans la base de donnée
     *
     * @param string $from depuis quelle table on souhaite faire la jointure
     * @param array $to Table cible de la joiture au format [Alias => Table] ou [Table]
     * @param string $on La condition de jointure
     * @param string $type Le type de jointure (Ex : LEFT, INNER, FULL, RIGHT)
     * @return $this Fluent
     */
    public function join($from, $to, $on, $type = 'INNER')
    {
        $this->joins[$from][] = [$type, $to, $on];
        return $this;
    }

    /**
     * Base de données Mysql - where
     *
     * Setter des conditions de la requête
     *
     * @param string $conditions Les conditions dans le WHERE
     * @return $this Fluent
     */
    public function where($conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }

    /**
     * Base de données Mysql - groupBy
     *
     * Setter des groupements de la requête
     *
     * @param string $group Les valeurs dans le GROUP BY
     * @return $this Fluent
     */
    public function groupBy($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * Base de données Mysql - having
     *
     * Setter des groupements de la requête
     *
     * @param string $having Les valeurs dans le HAVING
     * @return $this Fluent
     */
    public function having($having){
        $this->having = $having;
        return $this;
    }

    /**
     * Base de données Mysql - orderBy
     *
     * Choix des ordre dans la requête
     *
     * @param string $order Les valeurs dans le ORDER BY
     * @return $this Fluent
     */
    public function orderBy($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Base de données Mysql - limit
     *
     * Choix de la limite de la requête
     *
     * @param string $limit Les valeurs dans le LIMIT
     * @return $this Fluent
     */
    public function limit($limit){
        $this->limit = $limit;
        return $this;
    }

    /**
     * getSelect
     *
     * Permet de récupérer au format string la partie SELECT de la requête
     *
     * @return string
     */
    private function getSelect(){
        return 'SELECT ' . $this->columns . ' ';
    }

    /**
     * getInsertInto
     * Permet de récupérer au format string la partie INSERT INTO de la requête
     * @return string
     */
    private function getInsertInto(){
        return 'INSERT INTO ' . $this->insert . ' ' . $this->into . ' ';
    }

    /**
     * getDelete
     * Permet de récupérer au format string la partie DELETE FROM de la requête
     * @return string
     */
    private function getDelete(){
        return 'DELETE FROM ' . $this->delete . ' ';
    }

    /**
     * getInsertInto
     * Permet de récupérer au format string la partie VALUES de la requête Insert Into
     * @return string
     */
    private function getInsertValues(){
        $return = 'VALUES (';
        foreach($this->values as $key=>$val)
        {
            if(is_numeric($key))
            {
               $return .= ' ?,';
            }
            else{
                $return .= ' :' . $key . ',';
            }
        }
        $return = rtrim($return, ',');
        $return .= ' )';
        return $return;
    }

    /**
     * getUpdate
     * Permet de récupérer au format string la partie UPDATE de la requête
     * @return string
     */
    private function getUpdate(){

        if(is_array($this->set))
        {
            $set = '';
            foreach($this->set AS $col => $value)
            {
                $set .= $col . ' = :' . $col .', ';
                $this->updateValues[$col] = $value;
            }
            $set = rtrim($set, ', ');
        }
        else{
            $set = $this->set;
        }
        return 'UPDATE ' . $this->update . ' SET ' . $set;
    }

    /**
     * getFrom
     *
     * Permet de récupérer au format string la partie FROM de la requête
     *
     * @return string
     */
    private function getFrom(){
        $from = '
FROM ';
        foreach($this->tables as $alias => $table)
        {
            $from .= '`' . $table . '` ' . (is_numeric($alias) ? '' : $alias);
            $joins = [];
            if(isset($this->joins[$alias]))
            {
                $joins = $this->joins[$alias];
            }


            if(isset($this->joins[$table])) {
                $joins = array_merge($joins, $this->joins[$table]);
            }
               $from .= $this->doJoins($joins);
            $from .= ',
';
        }

        $from = rtrim($from, '
');
        $from = rtrim($from, ',');
        return $from;
    }

    /**
     * Fabrique les jointures
     *
     * Permet la récursivité afin de classer les jointures dans l'ordre
     *
     * @param $joins
     * @return string
     */
    private function doJoins(&$joins)
    {
        $joinsStr = '';
        foreach($joins AS $item)
        {

            $key = key($item[1]);
            if(!is_numeric($key))
                $al = $key;
            else
                $al = '';


            $joinsStr .= '
'                            . strtoupper($item[0]) . ' JOIN `' . $item[1][$key] . '` ' . $al . ' ON ' . $item[2];

            if(isset($this->joins[$item[1][$key]]))
            {
                $joinsStr .= $this->doJoins($item[1][$key]);
            }
            if(isset($this->joins[$al]))
            {
                $joinsStr .= $this->doJoins($this->joins[$al]);
            }
        }

        return $joinsStr;
    }

    /**
     * getWhere
     *
     * Permet de récupérer au format string la partie WHERE de la requête
     *
     * @return string
     */
    private function getWhere()
    {
        $where = '';
        if(!empty($this->conditions))
        {
            $where = '
WHERE ' . $this->conditions;
        }

        return $where;
    }

    /**
     * getGroup
     *
     * Permet de récupérer au format string la partie GROUP BY de la requête
     *
     * @return string
     */
    private function getGroup(){
        $groupBy = '';

        if(!empty($this->group))
        {
            $groupBy = '
GROUP BY ' . $this->group;
        }

        return $groupBy;
    }

    /**
     * getHaving
     *
     * Permet de récupérer au format string la partie HAVING de la requête
     *
     * @return string
     */
    private function getHaving(){
        $having = '';

        if(!empty($this->having))
        {
            $having = '
HAVING ' . $this->having;
        }

        return $having;
    }

    /**
     * getOrder
     *
     * Permet de récupérer au format string la partie ORDER BY de la requête
     *
     * @return string
     */
    private function getOrder(){
    $orderBy = '';

    if(!empty($this->order))
    {
        $orderBy = '
ORDER BY ' . $this->order;
    }

    return $orderBy;
    }

    /**
     * getLimit
     *
     * Permet de récupérer au format string la partie LIMIT de la requête
     *
     * @return string
     */
    private function getLimit(){
        $limit = '';

        if(!empty($this->limit))
        {
            $limit = '
LIMIT ' . $this->limit;
        }

        return $limit;
    }

    /**
     * Base de données Mysql - setValues
     *
     * Permet d'envoyer les valeurs à injecter dans la requête
     *
     * @param array $values Les valeurs à injecter si nécessaire
     * @return $this Fluent
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Base de données Mysql - prepare
     *
     * Préparation de la requête, sans execution
     *
     * @param string $key La clef afin de pouvoir retrouver la requête
     * @return bool Retourne vrai si la préparation s'est fait sans erreur, faux sinon
     */
    public function execute($key = 'default')
    {

        $state = false;
        try{
            $request = $this->toString();
            Motor::getDebug()->add('sql[]', $this->toString(true));

            if($req = @$this->pdo->prepare($request))
            {
                $values = $this->values;

                if($this->typeOfRequest == 'update')
                {
                    $values = array_merge($values, $this->updateValues);
                }

                if($req->execute($values))
                {
                    $this->requests[$key] = $req;
                    $state = true;
                }
                else
                {
                    $this->clear();
                }
            }
            else
            {
                $this->clear();
            }

        }catch(\Exception $e)
        {
            $this->clear();
        }

        return $state;
    }

    /**
     * Base de données Mysql - next
     *
     * Boucle sur une requête select
     *
     * @param string $key La clef afin de pouvoir retrouver la requête
     * @param bool $destroy Vrai si il faut détruire la requête, faux sinon
     * @return array|bool Retourne un tableau correspondant à la ligne correspondante si il reste des résultats, faux sinon
     */
    public function next($key = 'default', $destroy = true){

        if($this->exist($key))
        {
            if($rep = $this->requests[$key]->fetch(\PDO::FETCH_OBJ))
            {
                return $rep;
            }
            else
            {
                $this->requests[$key]->closeCursor();

                if($destroy)
                {
                    $this->clear();
                    unset($this->requests[$key]);
                }
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Base de données Mysql - destroy
     *
     * Détruire une requête
     *
     * @param string $key La clef afin de pouvoir retrouver la requête
     * @return array|bool Retourne un tableau correspondant à la ligne correspondante si il reste des résultats, faux sinon
     */
    public function destroy($key = 'default'){
        unset($this->requests[$key]);
        $this->clear();
    }

    /**
     * Base de données Mysql - describe
     *
     * Donne les champs de la table
     *
     * @param string $table La table pour laquelle on souhaite récupérer les champs
     * @return array Les champs de la table
     */
    public function describe($table){
        $req = $this->pdo->prepare('DESCRIBE ' . $table);
        Motor::getDebug()->add('sql[]', 'DESCRIBE ' . $table);
        $req->execute();
        $rep = $req->fetchAll(PDO::FETCH_OBJ);

        $return = [];

        foreach($rep AS $col)
        {
            $return[] = $col->Field;
        }

        return $return;
    }

    /**
     * Base de données Mysql - count
     *
     * Cette méthode permet de compter le nombre de résultats du précédent execute()
     * @param string $key
     * @param bool $withoutLimit
     * @return bool|int
     * @throws DatabaseException
     */
    public function count($key = 'default', $withoutLimit = false)
    {
        $count = 'count';

        if($withoutLimit)
        {
            $count = 'countWithoutLimit';

            if($this->typeOfRequest != 'select')
            {
                throw new DatabaseException('OPTION WITHOUT LIMIT CAN ONLY BE USED FOR A SELECT');
            }
        }

        if(is_null($this->$count))
        {
            if($withoutLimit)
            {
                $req = $this->pdo->prepare($this->getFullSelect(false));
                $req->execute($this->values);
                $this->countWithoutLimit = $req->rowCount();
            }
            else
            {
                if($this->exist($key))
                {
                    $this->count = $this->requests[$key]->rowCount();
                }
                else
                {
                    return false;
                }
            }
        }

        return $this->$count;
    }

    /**
     * Base de données Mysql - clear
     *
     * Cette méthode permet de remettre à zéro l'intégralité de la requête précedante
     */
    private function clear(){
        $this->columns = '';
        $this->tables = [];
        $this->joins = [];
        $this->conditions = '';
        $this->group = '';
        $this->having = '';
        $this->order = '';
        $this->limit = '';
        $this->values = [];
        $this->insert = '';
        $this->into = '';
        $this->update = '';
        $this->set = '';
        $this->delete = '';
        $this->updateValues = [];
        $this->count = null;
        $this->countWithoutLimit = null;
    }

    /**
     * Base de données Mysql - exist
     *
     * Vérifie si la requête $key éxiste bien
     *
     * @param string $key La clef de la requête à vérifier
     * @return bool Retourne vrai si la requête existe, faux sinon
     */
    public function exist($key){
        return isset($this->requests[$key]);
    }

    private function sort($a,$b){
        return strlen($b)-strlen($a);
    }
    /**
     * Base de données Mysql - withParams
     *
     * Permet d'injecter dans une requête SQL ses différents paramètres
     *
     * @param string $query La requête SQL a modifier
     * @param array $params Les paramètres à injecter
     * @return string Retourne la requête SQL avec ses paramètres
     */
    private function withValues($query, $params) {
        $keys = array();
        uasort($params, ['\PHX\Core\Database\MysqlDatabase', 'sort']);
        $values = $params;



        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_array($value))
                $values[$key] = implode(',', $value);

            if (is_null($value))
                $values[$key] = 'NULL';
        }
        // Walk the array to see if we can add single-quotes to strings
        array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".str_replace("\'", "\\\'", $v)."\'";'));
        $query = preg_replace($keys, $values, $query, 1);

        return $query;
    }


    private function getFullSelect($withLimit = true){
        $req = $this->getSelect();
        $req .= $this->getFrom();
        $req .= $this->getWhere();
        $req .= $this->getGroup();
        $req .= $this->getHaving();
        $req .= $this->getOrder();

        if($withLimit)
        {
            $req .= $this->getLimit();
        }

        return $req;
    }


    /**
     * Base de données Mysql - toString
     *
     * Permet de convertir la requête construite en chaine de caractères
     * @param bool $withValues Si il est nécessaire de convertir les valeurs
     * @return string La requête SQL générée
     * @throws DatabaseException
     */
    public function toString($withValues = false)
    {
        $req = '';
        if($this->typeOfRequest == 'select')
        {
            $req .= $this->getFullSelect();
        }
        elseif($this->typeOfRequest == 'insert')
        {
            $req .= $this->getInsertInto();
            $req .= $this->getInsertValues();
        }
        elseif($this->typeOfRequest == 'update')
        {
            $req .= $this->getUpdate();
            $req .= $this->getWhere();
        }
        elseif($this->typeOfRequest == 'delete')
        {
            $req .= $this->getDelete();
            $req .= $this->getWhere();
        }
        else
        {
            throw new DatabaseException('YOUR REQUEST IS NOT COMPLETE');
        }

        if($withValues)
        {
            if($this->typeOfRequest == 'update')
            {
                $values = array_merge($this->values, $this->updateValues);
                $req = $this->withValues($req, $values);
            }
            else
            {
                $req = $this->withValues($req, $this->values);
            }
        }
        return $req;
    }
}