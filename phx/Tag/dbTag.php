<?php
/**
 * Class dbTag
 *
 * @package fr.phx
 * @license MPL2
 */

namespace PHX\Tag;

use PHX\Core\Motor;
use PHX\Core\Router\Router;

/**
 * Tags {db*}
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class dbTag implements Tag
{
    /**
     * Nouvelle ligne pour le dbLoop
     * @var int
     */
    private static $newLine = null;

    /**
     * Méthode générique
     *
     * Permet de récupérer une liste des tags dépendants du tag envoyé
     *
     * @param string $tag Le tag pour lequel on souhaite connaitre les dépendences
     * @return array Retourne la liste des tags dépendants
     */
    public static function getSlaves($tag)
    {
        if($tag == 'dbLoop')
        {
            return ['dbStep', 'dbOpt'];
        }
        return [];
    }

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
    public static function run($subTag, $content, $options = null)
    {
        $method = 'run'.ucfirst($subTag);
        return self::$method($options, $content);
    }

    /**
     * TAG - dbSelect
     *
     * Permet de générer une requête SELECT à partir des données passés en paramètre
     *
     * @param array $options Les paramètres de la méthode
     * @return string
     */
    private static function runDbSelect($options){

        if(isset($options['in']) && (isset($options['t']) || isset($options['tables'])))
        {
            $req = Motor::getDb();

            $select = isset($options['columns']) ? $options['columns'] : isset($options['c']) ? $options['c'] : '*';
            $tables = isset($options['tables']) ? $options['tables'] : $options['t'];

            $from = [];
            $tablesList = [];

            foreach($tables AS $alias => $table)
            {
                // Si c'est une jointure
                if(is_array($table))
                {

                    $type = '';
                    if(isset($table['left']))
                        $type = 'LEFT';
                    elseif(isset($table['right']))
                        $type = 'RIGHT';
                    elseif(isset($table['FULL']))
                        $type = 'FULL';
                    elseif(isset($table['inner']))
                        $type = 'INNER';
                    else
                        return 'INVALID JOIN FOR TAG dbSelect : MISSING TYPEOF JOIN';

                    $joinFrom = $table[strtolower($type)];
                    unset($table[strtolower($type)]);

                    $cond='';

                    foreach($table as $key => $val)
                    {
                        if(is_numeric($key))
                        {
                            $cond = $val;
                        }
                        else
                        {
                            $tablesList[$key] = $val;
                            $table = [$key => $val];
                        }
                    }

                    $req->join($joinFrom, $table, $cond, $type);
                }
                else // Si c'est une table
                {
                    $from[$alias] = $table;
                    $tablesList[$alias] = $table;
                }
            }
            $req->from($from);

            if($select == '*')
            {
                $select = '';
                foreach($tables as $alias => $table)
                {
                    if(is_array($table))
                    {
                        next($table);
                        $alias = key($table);
                        $table = $table[$alias];
                    }

                    if(is_numeric($alias))
                    {
                        $alias = $table;
                    }

                    foreach($req->describe($table) AS $col)
                    {
                        $select .= $alias.'.'.$col.',';
                    }
                }
                $select = trim($select, ',');
            }

            $columns = explode(',', $select);
            $change = false;

            foreach($columns as $column)
            {
                $dots = explode('.', $column);
                $newSelect = '';
                if(isset($dots[1]) && $dots[1] == '*')
                {
                    $change = true;
                    $alias = $dots[0];
                    $table = $tablesList[$dots[0]];

                    foreach($req->describe($table) AS $col)
                    {
                        $newSelect .= $alias.'.'.$col.',';
                    }
                    $newSelect = trim($newSelect, ',');
                    $select = preg_replace('#' . preg_quote($column) . '#', $newSelect, $select);
                }
            }

            if($change)
            {
                $columns = explode(',', $select);
            }

            $columnsFinds = [];

            foreach ($columns as $column) {
                $fields = explode(' as ', $column);
                $fields[0] = preg_replace('#\s#', '', $fields[0]);

                // Début - Recensement du nombre de fois que l'on trouve une colonnne portant un même nom
                if (isset($fields[1])) {
                    $fields[1] = preg_replace('#\s#', '', $fields[1]);
                    $columnsFinds[] = $fields[1];
                } else {
                    $dot = explode('.', $fields[0]);

                    if (isset($dot[1])) {
                        $columnsFinds[] = $dot[1];
                    } else {
                        $columnsFinds[] = $fields[0];
                    }
                }
                // Fin - Recensement du nombre de fois que l'on trouve une colonnne portant un même nom
            }
            $columnsFinds = array_count_values($columnsFinds);

            // Début préfixage malin afin d'éviter les colisions
            $cols = '';
            foreach ($columns as $column) {
                $fields = explode(' as ', $column);
                $fields[0] = preg_replace('#\s#', '', $fields[0]);

                if (isset($fields[1])) {
                    $fields[1] = preg_replace('#\s#', '', $fields[1]);
                    $alias = 'PHXal_' . $fields[1];
                } else {
                    // Si c'est une fonction d'agrégation
                    if (preg_match('#^([\w]+)\(.+\)$#', $fields[0], $out)) {
                        $alias = 'PHXal_' . strtolower($out[1]);
                    } else {
                        $dot = explode('.', $fields[0]);
                        if (isset($dot[1])) {
                            if ($columnsFinds[$dot[1]] > 1) {
                                $alias = 'PHXal_' . $dot[0] . '_' . $dot[1];
                            } else {
                                $alias = 'PHXal_' . $dot[1];
                            }
                        } else {
                            $alias = 'PHXal_' . $fields[0];
                        }
                    }
                }
                $cols .= $fields[0] . ' AS ' . $alias . ', ';
            }
            $cols = rtrim($cols, ', ');
            // Fin préfixage malin afin d'éviter les colisions

            $req->select($cols);

            isset($options['where']) ? $req->where($options['where']) : isset($options['w']) ? $req->where($options['w']):'';
            isset($options['group']) ? $req->groupBy($options['group']) : isset($options['g']) ? $req->groupBy($options['g']):'';
            isset($options['having']) ? $req->having($options['having']) : isset($options['h']) ? $req->having($options['h']):'';
            isset($options['order']) ? $req->orderBy($options['order']) : isset($options['o']) ? $req->orderBy($options['o']):'';
            isset($options['limit']) ? $req->limit($options['limit']) : isset($options['l']) ? $req->limit($options['l']):'';

            $values = isset($options['values']) ? $options['values'] : isset($options['v']) ? $options['v'] : [];

            $req->setValues($values);


            if(!$req->execute((string)$options['in']))
            {
                return 'DBSELECT : AN ERROR HAS OCCURED';
            }
            else
            {
                return '';
            }

        }
        else
        {
            return 'INVALID PARAMETERS FOR TAG dbSelect : MINIMUM in, tables (or t)';
        }
    }

    /**
     * TAG - dbInsert
     *
     * Permet de générer une requête INSERT à partir des données passés en paramètre
     *
     * @param array $options Les paramètres de la méthode
     * @return string
     */
    private static function runDbInsert($options){
        if((isset($options['t']) || isset($options['table'])) && (isset($options['v']) || isset($options['values'])))
        {
            $table = isset($options['table']) ? $options['table'] : $options['t'];
            $values = isset($options['values']) ? $options['values'] : $options['v'];
            $into = isset($options['columns']) ? $options['columns'] : isset($options['c']) ? $options['c'] : '';

            $db = Motor::getDb();

            $db->insert($table)->into($into)->setValues($values);

            $db->execute('');
        }
        else
        {
            return 'INVALID PARAMETERS FOR TAG dbInsert : MINIMUM tables (or t), values (or v)';
        }
    }

    /**
     * TAG - dbUpdate
     *
     * Permet de générer une requête UPDATE à partir des données passés en paramètre
     *
     * @param array $options Les paramètres de la méthode
     * @return string
     */
    private static function runDbUpdate($options)
    {
        if ((isset($options['t']) || isset($options['table'])) && (isset($options['v']) || isset($options['values'])) && (isset($options['s']) || isset($options['set'])))
        {
            $table = isset($options['table']) ? $options['table'] : $options['t'];
            $values = isset($options['values']) ? $options['values'] : $options['v'];
            $set = isset($options['set']) ? $options['set'] : $options['s'];
            $where = isset($options['where']) ? $options['where'] : isset($options['w']) ? $options['w'] : '';

            $db = Motor::getDb();
            $db->update($table)->set($set)->setValues($values);

            if($where != '')
            {
                $db->where($where);
            }

            $db->execute('');
            return '';
        }
        else
        {
            return 'INVALID PARAMETERS FOR TAG dbUpdate : MINIMUM tables (or t), values (or v), set (or s)';
        }
    }

    /**
     * TAG - dbDelete
     *
     * Permet de générer une requête DELETE à partir des données passés en paramètre
     *
     * @param array $options Les paramètres de la méthode
     * @return string
     */
    private static function runDbDelete($options)
    {
        if(isset($options['table']) || isset($options['t']))
        {
            $table = isset($options['table']) ? $options['table'] : $options['t'];
            $where = isset($options['where']) ? $options['where'] : isset($options['w']) ? $options['w'] : '';
            $values = isset($options['values']) ? $options['values'] : isset($options['v']) ? $options['v'] : [];

            $db = Motor::getDb();
            $db->delete($table)->where($where)->setValues($values);
            $db->execute('');
            return '';
        }
    }


    /**
     * TAG - dbOpt
     *
     * Ce tag permet d'insérer des options pour les différents tags db
     * @param array $options Les paramètres de la méthode
     * @return string
     */
    private static function runDbOpt($options){
        if(isset($options['newline']))
        {
            self::$newLine = intval($options['newline']);
        }
        return '';
    }

    /**
     * TAG - dbStep
     *
     * Affiche le content en fonction du curseur dbLoop (Le traitement se fait au niveau de dbLoop()
     * @param array $options Les paramètres de la méthode
     * @return string
     */
    public static function runDbPages($options, $content){

        if(isset($options['route']) || isset($options['r'])) {
            $allowed_models = ['default'];

            $model = isset($options['modele']) ? $options['modele'] : (isset($options['m']) ? $options['m'] : 'default');
            $for = isset($options['for']) ? $options['for'] : (isset($options['f']) ? $options['f'] : 'default');
            $limit = isset($options['limit']) ? $options['limit'] : (isset($options['l']) ? $options['l'] : 1);
            $current = isset($options['current']) ? $options['current'] : (isset($options['c']) ? $options['c'] : 1);
            $route = isset($options['route']) ? $options['route'] : $options['r'];

            if (!in_array($model, $allowed_models)) {
                $model = 'default';
            }

            if ($model == 'default') {
                $root = rtrim(Motor::getConf('site')->get('siteUrl'), '/') . '/';
                $max = Motor::getDb()->count($for, true);

                // Page courante
                preg_match('#\{(.*?page_current.*?)\}#i', $content, $out);
                $toReplace = $out[0];
                $replacement = preg_replace('#page_current#i', $current, $out[1]);
                $content = preg_replace('#' . preg_quote($toReplace, '#') . '#', $replacement, $content);

                // Page précédente
                preg_match('#\{(.*?page_prev.*?)\}#i', $content, $out);
                $toReplace = $out[0];
                if (intval($current) != 1) {
                    $replacement = preg_replace('#page_prev#i', '<', $out[1]);
                } else {
                    $replacement = '';
                }
                $content = preg_replace('#' . preg_quote($toReplace, '#') . '#', $replacement, $content);
                $content = preg_replace('#route_prev#i', $root . Router::getUrl($route, 'GET', ['page' => (intval($current)-1)]), $content);

                // Page suivante
                preg_match('#\{(.*?page_next.*?)\}#i', $content, $out);
                $toReplace = $out[0];
                if (intval($current) * intval($limit) < $max) {
                    $replacement = preg_replace('#page_next#i', '>', $out[1]);
                } else {
                    $replacement = '';
                }
                $content = preg_replace('#' . preg_quote($toReplace, '#') . '#', $replacement, $content);
                $content = preg_replace('#route_next#i', $root . Router::getUrl($route, 'GET', ['page' => (intval($current)+1)]), $content);

                // Page first
                preg_match('#\{(.*?page_first.*?)\}#i', $content, $out);
                $toReplace = $out[0];
                if (intval($current) != 1) {
                    $replacement = preg_replace('#page_first#i', '&laquo;', $out[1]);
                } else {
                    $replacement = '';
                }
                $content = preg_replace('#' . preg_quote($toReplace, '#') . '#', $replacement, $content);
                $content = preg_replace('#route_first#i', $root . Router::getUrl($route, 'GET', ['page' => 1]), $content);

                // Page last
                preg_match('#\{(.*?page_last.*?)\}#i', $content, $out);
                $toReplace = $out[0];
                $last = ceil(($max / $limit));
                if (intval($current) < $last) {
                    $replacement = preg_replace('#page_last#i', '&raquo;', $out[1]);
                } else {
                    $replacement = '';
                }
                $content = preg_replace('#' . preg_quote($toReplace, '#') . '#', $replacement, $content);
                $content = preg_replace('#route_last#i', $root . Router::getUrl($route, 'GET', ['page' => $last]), $content);

            }


            return $content;
        }else{
            return 'INCORRECT PARAM FOR TAG dbPages : MINIMUM route (or r)';
        }
    }

    /**
     * TAG - dbStep
     *
     * Affiche le content en fonction du curseur dbLoop (Le traitement se fait au niveau de dbLoop()
     * @param array $options Les paramètres de la méthode
     * @return string
     */
    private static function runDbStep($options, $content){
        if(isset($options['step']) || isset($options['s']))
        {
            $begin = isset($options['begin']) ? $options['begin'] : (isset($options['b']) ? $options['b'] : '1');
            $step = isset($options['step']) ? $options['step'] : $options['s'];
            $ignore = isset($options['ignorenewline']) ? '1' : (isset($options['inl']) ? '1' : '0');

            return '@PHX_TAG_DBSTEP:' . $begin . ',' . $step . ',' . $ignore . '@' . $content . '@/PHX_TAG_DBSTEP@';
        }

        return 'INVALID PARAMETERS FOR TAG dbStep : MINIMUM step (or s)';
    }


    /**
     * TAG - dbLoop
     *
     * Permet de boucler sur le résultat d'une précédente requête SELECT
     *
     * @param array $options Les paramètres de la méthode
     * @param string $content Le contenu entre les balises {dbLoop} et {/dbLoop}
     * @return string
     */
    private static function runDbLoop($options, &$content){

        if(isset($options['for']))
        {
            $db = Motor::getDb();
            if($db->exist($options['for']))
            {
                $return = '';
                $i = 0;
                $iLine = 0;
                $count =  $db->count($options['for']);
                if(self::$newLine != null)
                {
                    $newLine = self::$newLine;
                }
                else
                {
                    $newLine = $count;
                }

                while($rep = $db->next($options['for']))
                {
                    $datas = $content;
                    $i++;
                    $iLine++;


                    preg_match_all('#@PHX_TAG_DBSTEP:([0-9]+),((?:[0-9]+)|top|bot),([0-9]+)@(.*?)@/PHX_TAG_DBSTEP@#is', $datas, $out, PREG_SET_ORDER);
                    if(count($out) > 0)
                    {
                        foreach($out as $step)
                        {

                            if($step[3] == '1')
                            {
                                $it = $i;
                            }
                            else
                            {
                                $it = $iLine;

                                if ($i == $count) // Si on est a la fin des enregistrements
                                {
                                    $newLine = $it;
                                }
                            }

                            if(
                                $it >= intval($step[1]) && (
                                ($step[2] == 'top' && $it == 1) ||
                                ($step[2] == 'bot' && $it == $newLine) ||
                                ($step[2] != 'bot' && $step[2] != 'top' && $it%$step[2] == 0)
                            ))
                            {
                                 $datas = preg_replace('#@PHX_TAG_DBSTEP:([0-9]+),((?:[0-9]+)|top|bot),([0-9]+)@(.*?)@/PHX_TAG_DBSTEP@#', $step[4], $datas, 1);
                            }
                            else
                            {
                                $datas = preg_replace('#@PHX_TAG_DBSTEP:([0-9]+),((?:[0-9]+)|top|bot),([0-9]+)@(.*?)@/PHX_TAG_DBSTEP@#', '', $datas, 1);
                            }
                        }
                    }

                    if($iLine >= self::$newLine)
                    {
                        $iLine=0;
                    }

                    $return .= $datas;
                    if(isset($rep)) {
                        if (preg_match_all('#\{'.(string)$options['for'].'(?:\[([\w_]+)\])?->([\w_]+)\}#s', $content, $values, PREG_SET_ORDER)) {
                            foreach($values as $value)
                            {
                                $toReplace = $value[0];
                                if(!empty($value[1]))
                                {
                                    if(isset($rep->{'PHXal_' . $value[1] . '_' . $value[2]}))
                                    {
                                        $toReplace = $rep->{'PHXal_' . $value[1] . '_' . $value[2]};
                                    }
                                    elseif(isset($rep->{'PHXal_' . $value[2]}))
                                    {
                                        $toReplace = $rep->{'PHXal_' . $value[2]};
                                    }
                                }
                                else
                                {
                                    if(isset($rep->{'PHXal_' . $value[2]}))
                                    {
                                        $toReplace = $rep->{'PHXal_' . $value[2]};
                                    }

                                }

                                $mode = isset($options['mode']) ? $options['mode'] : (isset($options['m']) ? $options['m'] : '');

                                switch($mode)
                                {
                                    case 'txt':
                                        $toReplace = htmlspecialchars($toReplace);
                                    break;
                                }


                                $regex = '#' . preg_quote($value[0], '#') . '#';
                                $return = preg_replace($regex, $toReplace, $return);
                            }
                        }
                    }
                }

                self::$newLine = null;
                return $return;
            }
            else
            {
                return 'DBLOOP : REQUEST "' . htmlspecialchars($options['for']) . '" DOES NOT EXIST';
            }
        }
        else
        {
            return 'INVALID PARAMETERS FOR TAG dbLoop : MISSING for ARG';
        }
    }

}