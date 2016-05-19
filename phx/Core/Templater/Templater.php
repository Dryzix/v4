<?php
/**
 * Class Templater
 *
 * @package fr.phx
 * @license MPL2
 */

namespace PHX\Core\Templater;

use PHX\Core\Cache\Cache;
use PHX\Core\Motor;

/**
 * Moteur de template
 * @author Tom BACCI <tom.bacci@hotmail.fr>
 * @version 0.1
 */
class Templater{

    /**
     * Tags du moteur
     *
     * @var array
     */
    private $tags;
    /**
     * Tags du site
     *
     * @var array
     */
    private $customTagNamespace;
    /**
     * État du cache
     *
     * @var bool
     */
    private $useCache;

    /**
     * Variables présentes dans le template
     *
     * @var bool
     */
    private $vars = [];


    /**
     * Constructeur
     *
     * Charge la configuration du moteur et du site
     */
    public function __construct()
    {
        $this->customTagNamespace = [];
        $this->tags['PHX'] = Motor::getConf()->get('Templater')->tags;

        if(Motor::getConf('site')->get('Templater'))
        {
            $this->customTagNamespace = Motor::getConf('site')->get('Templater')->tagsNamespace;
            $this->customTagNamespace = trim($this->customTagNamespace, '\\').'\\';
            $this->tags['CUSTOM'] = Motor::getConf('site')->get('Templater')->tags;
        }

        if(Motor::getConf('site')->get('Cache'))
        {
            $this->useCache = Motor::getConf('site')->get('Cache')->use;
            $cachePath = Motor::getConf('site')->get('Cache')->path;
            $cachePath = rtrim($cachePath, '\\').'\\';
            $this->cache = new Cache($cachePath);
        }
    }


    /**
     * Interpréteur de template
     *
     * Cette fonction permet de charger un Template ainsi que d'y appliquer les différents tags configurés
     *
     * @param string $filepath Le chemin vers le .tpl à charger
     * @param string $filePathOrigin Ne pas renseigner (uniquement pour le moteur)
     * @param array $tree
     * @param bool $first Ne pas renseigner (uniquement pour le moteur)
     * @param null $relative Ne pas renseigner (uniquement pour le moteur)
     * @return string Retourne le HTML à afficher
     */
    public function loadTemplate($filepath, $filePathOrigin = '', $tree = [], $first = true, $relative = null, $doTheTree = true)
    {
        if(file_exists($filepath))
        {
            $page = file_get_contents($filepath);

            // Suppression des + des ajouts de vars
            $regex = '#"\+\{\{#is';
            $page = preg_replace($regex, '{{', $page);
            $regex = '#\}\}\+"#is';
            $page = preg_replace($regex, '}}', $page);

            // Suppression des tags commentés
            $regex = '#\{\*(.*?)\*\}\n?#is';
            $page = preg_replace($regex, '', $page);


            $page = $this->loadVars($page);
            $page =  $this->runTags($page);

        }
        else
        {
            $page =  'FILE "' . $filepath . '" DOES NOT EXIST';
        }
        if($doTheTree)
        {
            $tree = $this->makeTree($filepath, $page);
            $tree = array_shift($tree);
        }
        if($first) {
            $this->tree = $tree;
            Motor::getDebug()->add('tree', $tree);

            $filePathOrigin = $filepath;
        }else{
            if($relative !== null)
            {
                $filepath = $this->resolveRelative($filePathOrigin, $relative);
            }
        }


        if(is_array($tree))
        {
            foreach ($tree as  $tpl) {
                if (is_array($tpl)) {
                    foreach($tpl as $relative => $treee)
                    {
                        $load = $this->loadTemplate($this->resolveRelative($filepath, $relative), $filepath, $treee, false, $relative, false);
                        $regex = '#{tpl}' . preg_quote($relative, '#') . '{/tpl}#is';
                        $page = preg_replace($regex, $load, $page, 1);
                        $page = $this->loadVars($page);
                    }
                } else {
                    if(file_exists($tpl))
                    {
                        $load =  $this->loadTemplate($tpl, '', [], '', false, null, false);
                        $tpl = str_replace(Motor::getConf('site')->get('rootPath'), '~/', $tpl);
                    }
                    else
                    {
                        $load =  $this->loadTemplate($this->resolveRelative($filepath, $tpl), [], '', false, null, false);
                    }
                    $regex = '#{tpl}' . preg_quote($tpl, '#') . '{/tpl}#is';
                    $page = preg_replace($regex, $load, $page, 1, $count);
                    $page = $this->loadVars($page);
                }
            }
        }
        $page = $this->loadVars($page);
        $page =  $this->runTags($page);

        if(preg_match('#{tpl}(.*?){/tpl}#is', $page))
        {
            $page = $this->loadTemplate($filepath, [], '', false, null, true);
        }
        return $page;
    }




    /**
     * Interpréteur de tags
     *
     * Interprète les différents tags situés dans le fichier passé en paramètre
     *
     * @param string $tpl Contenu du .tpl à interpréter
     * @param array $toInterpret
     * @return string Retourne le .tpl interprété
     */

    private function runTags($tpl, $toInterpret = [])
    {


        // Début - recherche de tout les tags
        $regex = '#{([a-z0-9_-]+)(.*?)?(?:}(.*?){/\1}|(?:\s*)/})#is';
        preg_match_all($regex, $tpl, $finds);

        $finds = $finds[0];
        // Fin - recherche de tout les tags

        // Début - boucle sur tout les présumés tags
        foreach($finds as $tagFind) {


            // On vérifie si c'est un tag inline ou balise
            $regex = '#^{([a-z0-9_-]+)(.*?)?}(.*?){/\1}$#is';
            if (preg_match($regex, $tagFind, $finds2)) {
                $tagName = $finds2[1];
                $content = $finds2[3];
            } else {
                $regex = '#{([a-z0-9_]+)([^}]*?)/}#is';
                preg_match($regex, $tagFind, $finds2);
                $tagName = $finds2[1];
                $content = '';
            }
            // On évite le tag tpl car il est géré dans loadTemplate()
            if($tagName != 'tpl' && (count($toInterpret) == 0 || in_array($tagName, $toInterpret))) {
                // Récupération des paramètres du tag
                $regex = '#^{([a-z0-9_-]+)\s(.*?)/?}#i';
                preg_match($regex, $tagFind, $optionsTag);
                $optionArg = [];
                if (count($optionsTag) != 0) {
                    $tagsParam =  $optionsTag[2];
                    $optionsTag = [];

                    do{
                        preg_match('#(.*?)(\s([a-z0-9_]+)=|\s*$)#i', $tagsParam, $paramsTag);
                        $paramsTag[1] = preg_replace('#(.*?)(?:\s([a-z0-9_]+)=|\s*$)#', '\1', $paramsTag[1]);
                        $optionsTag[] = $paramsTag[1];

                        $regex = '#' . preg_quote($paramsTag[1], '#') . '\s*#';
                        $tagsParam = preg_replace($regex, '', $tagsParam);

                    }while(strlen($tagsParam) != 0);


                    foreach ($optionsTag as $optionTag) {
                        preg_match('#([\w]+)=(.+)#', $optionTag, $vals);
                        array_shift($vals);


                        // Si les paramètres parraissent valides
                        if (count($vals) == 2) {
                            // Si c'est un tableau
                            if(preg_match('#\[(.*)\]#i',$vals[1],$out))
                            {
                                $tab = $this->convertStringToTab($out[1]);

                                $optionArg[$vals[0]] = $tab;
                            }
                            else
                            {
                                // Suppression des quotes et guillemets et des faux espaces
                                if(preg_match('#^(\'|")(.*?)(\'|")$#is', $vals[1], $out))
                                {
                                    $vals[1] = $out[2];
                                }

                                $optionArg[$vals[0]] = $vals[1];
                            }

                        }

                    }
                }


                $class = $this->getTagClass($tagName);

                if(!is_null($class))
                {
                    // Si l'on trouve des tags à interpréter dans un tag, on relance runTags()
                    $regex = '#{([a-z0-9_-]+)([^/]*?)?(?:}(.*?){/\1}|/})#is';
                    if(preg_match_all($regex, $content, $out))
                    {
                        $slaves = $class::getSlaves($tagName);

                        foreach($out[1] as $tag)
                        {
                            if(in_array($tag, $slaves))
                            {
                                $content = $this->runTags($content, $slaves);
                            }
                        }
                        $content = $this->loadVars($content);
                    }


                    // On lance le tag
                    $regex = '#' . preg_quote($tagFind, '#') . '#is';
                    $tpl = preg_replace($regex, $class::run($tagName, $content, $optionArg), $tpl, 1);
                }
                else {
                    $regex = '#' . preg_quote($tagFind, '#') . '#is';
                    $replacement = htmlentities($tagFind, ENT_HTML5);
                    $tpl = preg_replace($regex, $replacement, $tpl);
                }


                // Fin - Chargement du namespace si Tags customs
            }
        }
        // Fin - boucle sur tout les présumés tags

        // Début - recherche de tout les tags si pas d'esclaves
        if(count($toInterpret) == 0) {
            $regex = '#{(?!tpl)(.*?)?(?:}(.*?){/\1}|(?:\s*)/})#is';
            preg_match_all($regex, $tpl, $finds);

            $finds = $finds[0];

            if (count($finds) != 0) {
                $tpl = $this->runTags($tpl);
            }
        }
        // Fin - recherche de tout les tags si pas d'esclaves

        return $tpl;
    }

    /**
     * Gestionnaire des variables
     *
     * Cette méthode s'occupe de charger et injecter les différentes variables d'un .tpl
     *
     * @param string $content Le contenu tu .tpl
     * @return string Le .tpl avec ses variables chargés
     */
    private function loadVars($content)
    {

        if(preg_match('#^(.*?){tpl\}#is', $content, $out))
        {
            $toReplace = '#' . preg_quote($out[0], '#') . '#is';
            $searchTags = $out[1];
        }else{
            $searchTags = &$content;
            $toReplace = '#(.*?)#is';
        }


        // Recherche des variables template
        $regex= '#{{([a-z0-9_]+)(\+\+|--|(-=|\+=|\*=|\.=|=)(.*?))?(\}\})?\}\}#is';
        preg_match_all($regex, $searchTags, $vars, PREG_SET_ORDER);

        // Récupération de la valeur des variables
        if(count($vars) != 0)
        {
            for($i = 0;$i<count($vars);$i++)
            {
                // $vars[$i][1] Correspond au nom de la variable {{key}}
                // $vars[$i][2] Correspond a "++" dans le cas d'un {{i++}}
                // $vars[$i][4] Correspond a la valeur de la variable (si {{key=val}})

                if(isset($vars[$i][2]) && $vars[$i][2] == '++')
                {
                    $this->incrementIntVar($vars[$i][1]);
                    $regex = '#' . preg_quote($vars[$i][0], '#') . '#';
                    $content = preg_replace($regex, '', $content);
                }
                elseif(isset($vars[$i][2]) && $vars[$i][2] == '--')
                {
                    $this->decrementIntVar($vars[$i][1]);
                    $regex = '#' . preg_quote($vars[$i][0], '#') . '#';
                    $content = preg_replace($regex, '', $content);
                }
                elseif(isset($vars[$i][3]) && $vars[$i][3] == '+=')
                {
                   if(preg_match('#^\{\{([0-9a-z_]+)\}\}#i', $vars[$i][4], $out))
                   {
                       $vars[$i][4] = $this->getVar($out[1]);
                   }
                        $this->incrementIntVar($vars[$i][1], intval($vars[$i][4]));
                        $regex = '#' . preg_quote($vars[$i][0], '#') . '#';
                        $content = preg_replace($regex, '', $content);
                }
                elseif(isset($vars[$i][3]) && $vars[$i][3] == '*=')
                {
                    if(preg_match('#^\{\{([0-9a-z_]+)\}\}#i', $vars[$i][4], $out))
                    {
                        $vars[$i][4] = $this->getVar($out[1]);
                    }

                    $this->vars[$vars[$i][1]] = (intval($this->vars[$vars[$i][1]])*intval($vars[$i][4]));
                    $regex = '#' . preg_quote($vars[$i][0], '#') . '#';
                    $content = preg_replace($regex, '', $content);
                }
                elseif(isset($vars[$i][3]) && $vars[$i][3] == '-=')
                {
                    if(preg_match('#^\{\{([0-9a-z_]+)\}\}#i', $vars[$i][4], $out))
                    {
                        $vars[$i][4] = $this->getVar($out[1]);
                    }
                    $this->decrementIntVar($vars[$i][1], intval($vars[$i][4]));
                    $regex = '#' . preg_quote($vars[$i][0], '#') . '#';
                    $content = preg_replace($regex, '', $content);
                }
                elseif(isset($vars[$i][3]) && $vars[$i][3] == '.=')
                {
                    if(preg_match('#^\{\{([0-9a-z_]+)\}\}#i', $vars[$i][4], $out))
                    {
                        $vars[$i][4] = $this->getVar($out[1]);
                    }

                    // Suppression des quotes et guillemets
                    $vars[$i][4] = preg_replace('#^(\'|")(.*?)(\'|")$#is', "$2", $vars[$i][4]);
                    $vars[$i][4] = trim($vars[$i][4], '[]');

                    $this->incrementStringVar($vars[$i][1], $vars[$i][4]);
                    $regex = '#' . preg_quote($vars[$i][0], '#') . '#';
                    $content = preg_replace($regex, '', $content);
                }
                else {
                    if (isset($vars[$i][4])) {
                        $vars[$i][4] = preg_replace('#\n#s', '', $vars[$i][4]);
                        $vars[$i][4] = preg_replace('#\t#s', '', $vars[$i][4]);

                        if(preg_match('#^=\{\{(.+?)$#', $vars[$i][2], $otherVar))
                        {
                            $vars[$i][4] = $this->getVar($otherVar[1]);
                            $vars[$i][2] = '=' . $this->getVar($otherVar[1]);
                        }


                        // Suppression du = au début (=valeur)
                        $vars[$i][2] = ltrim($vars[$i][2], '=');

                        // Si c'est un tableau
                        if(preg_match('#\[(.*)\]#i',$vars[$i][4],$out))
                        {
                            $tab = $this->convertStringToTab($out[1]);
                            $this->setVar($vars[$i][1], $tab);
                        }
                        else
                        {
                            // Suppression des quotes et guillemets et des faux espaces
                            if(preg_match('#(\'|")(.*)(\'|")#is', $vars[$i][4], $out))
                            {
                                $vars[$i][4] = $out[2];
                            }

                            $this->setVar($vars[$i][1], $vars[$i][4]);
                        }

                        $regex = '#' . preg_quote($vars[$i][0], '#') . '#';
                        $content = preg_replace($regex, '', $content);
                    } else {
                        $regex = '#{{' . preg_quote($vars[$i][1], '#') . '}}#';
                        $content = preg_replace($regex, $this->getVar($vars[$i][1]), $content, 1);
                    }
                }
            }
        }

        return $content;
    }

    /**
     * Convertir un sTableau en String
     *
     * Cette méthode permet de convertir un tableau stocké dans le moteur de template en chaine de caractère lisible pour l'utilisateur
     *
     * @param array $tab
     * @return string
     */
    private function convertTabToString($tab)
    {
        $string = '[';
        foreach($tab as $key => $var)
        {
            if(!is_numeric($key))
            {
                $string .= '"' . $key . '"->';
            }
            if(is_numeric($var))
            {
                $string .=  $var;
            }
            elseif(is_array($var))
            {
                $string .= $this->convertTabToString($var);
            }
            else
            {
                $string .= '"'.$var.'"';
            }
            $string .= ',';
        }
        $string = rtrim($string, ',');
        $string .=']';

        return $string;
    }

    /**
     * Convertir un String en Tableau
     *
     * Cette méthode permet de convertir un string qui forme un tableau en un tableau qui peut être stocké dans le moteur de template sous forme d'un array
     *
     * @param string $string
     * @return array
     */
    private function convertStringToTab($string){


        $string = trim($string, " ");
        $string = preg_replace("#\n#s", '', $string);
        $string = preg_replace("#\t#", '', $string);
        preg_match_all('#([^[]+?|[^\[]*->[^\[]*\[.+?\]|\[.+?\])(?:,|$)#', $string, $out);
        $values = $out[1];
        $tab = [];

        foreach($values as $value)
        {
            preg_match('#([^>]+)->(.*)(?:]$|],|,|$)#i', $value, $out);
            array_shift($out);
            $vvalues  = $out;

            // Si c'est un tableau clef -> valeur
            if(count($vvalues) > 1)
            {
                $key = $vvalues[0];
                $val = $vvalues[1];

                // Suppression des quotes et guillemets et des faux espaces
                if (preg_match('#(\'|")(.*?)(\'|")#is', $key, $out)) {
                    $key = $out[2];
                }

                // Si c'est un tableau
                if (preg_match('#\[(.*)\]#i', $val, $out)) {
                    $val = $this->convertStringToTab($out[1]);
                } else {
                    // Suppression des quotes et guillemets et des faux espaces
                    if (preg_match('#(\'|")(.*?)(\'|")#is', $val, $out)) {
                        $val = $out[2];
                    }
                }


                $tab[$key] = $val;
            }
            else
            {
                // Si c'est un tableau
                if(preg_match('#\[(.*)\]#i',$value,$out)) {
                    $value = $this->convertStringToTab($out[1]);
                }
                else
                {
                    // Suppression des quotes et guillemets et des faux espaces
                    if(preg_match('#(\'|")(.*?)(\'|")#is', $value, $out))
                    {
                        $value = $out[2];
                    }
                    else{
                        $value = preg_replace('#\s#', '', $value);
                    }
                }
                $tab[] = $value;
            }
        }
        return $tab;
    }


    /**
     * Résout un chemin
     *
     * @param string $originPath Chemin du fchier d'origine
     * @param string $relativePath Chemin du fchier à retrouver
     * @return string Retourne le chemin vers le fichier demandé
     */
    private function resolveRelative($originPath, $relativePath){
        return realpath(dirname($originPath).'/'.$relativePath);
    }

    /**
     * Constructeur d'arborescence
     *
     * Construit l'arbre des dépendences d'un template (cherche tout les enfants)
     * à partir de la balise {tpl} dans les templates
     *
     * @param string $filepath Chemin vers le .tpl
     * @param string $tpl  Contenu du .tpl
     * @param string $relativePath Chemin relatif de l'enfant à résoudre
     * @return array Retourne le tableau des enfants du .tpl
     */
    private function makeTree($filepath, $tpl, $relativePath = ''){
        if($relativePath == '')
            $relativePath = $filepath;

        if(empty($tpl))
        {
            $tpl = file_get_contents($filepath);
        }

        $tree = [];
        $regex = '#{\*{(.*?)}\*}#is';
        $tpl = preg_replace($regex, '', $tpl);
        preg_match_all('#{tpl}(.*?){/tpl}#is', $tpl, $includes);
        array_shift($includes);
        foreach ($includes[0] as $include) {
            if(preg_match('#^~/(.*?)$#', $include, $out))
            {
                $include = Motor::getConf('site')->get('rootPath'). $out[1];
                $child = $this->makeTree($include, '', '');
            }
            else
            {
                $child = $this->makeTree($this->resolveRelative($filepath,$include), '', $include);
            }
            if($child == [])
            {
                $tree[$relativePath][] = $include;
            }
            else
            {
               $tree[$relativePath][] = $child;
            }
        }
        return $tree;
    }

    /**
     * Récupérer la class d'un tag
     * @param string $tag Le tag pour lequel on souhaite connaitre la class
     * @return null|string La class du tag si trouvé, null sinon
     */
    private function getTagClass($tag){

        $class = null;

        // Début - Boucle sur les taglist moteurs puis custom
        foreach ($this->tags as $where => $tagsList) {
            // Début - Boucle sur les différents tags
            foreach ($tagsList as $parentTag => $subTags) {
                // Début - Boucle sur les différents sous-tags
                foreach ($subTags as $tagName) {
                    if($tag == $tagName)
                    {
                        // Début - Chargement du namespace si Tags customs
                        if ($where == 'CUSTOM') {
                            $class = '\\' . $this->customTagNamespace . $parentTag . 'Tag';
                        } else {
                            $class = '\PHX\Tag\\' . $parentTag . 'Tag';
                        }
                        // Fin - Chargement du namespace si Tags customs
                    }
                }
            }
        }
        return $class;
    }

    /**
     * Retourne une variable
     *
     * Cette méthode vérifie si la variable a bien été déclaré dans le template, si oui elle renvoi sa valeure
     * si non, elle renvoi le nom de la variable
     *
     * @param string $key Le nom de la variable
     * @return string La valeur de la variable
     */
    private function getVar($key)
    {
        if(isset($this->vars[$key]))
        {
            if(is_array($this->vars[$key]))
            {
                $tab = $this->convertTabToString($this->vars[$key]);
                return $tab;
            }

            $val = $this->runTags($this->vars[$key]);

            // Début - recherche tags dans variable
            $regex = '#{([a-z0-9_-]+)(.*?)?(?:}(.*?){/\1}|(?:\s*)/})#is';

            if(preg_match($regex, $val))
            {
                $val = $this->runTags($val);
            }

            $val = preg_replace('#\(#', '&#x28;', $val);
            $val = preg_replace('#\)#', '&#x29;', $val);
            $val = preg_replace('#\[#', '&#x5B;', $val);
            $val = preg_replace('#\]#', '&#x5D;', $val);
            return $val;
        }
        else
        {
            return '&#x7B;&#x7B;'.$key.'&#x7D;&#x7D;';
        }
    }

    /**
     *
     * Injecte une variable
     *
     * @param string $key Le nom de la variable
     * @param string|array $val La valeur de la variables
     */
    private function setVar($key, $val)
    {
        $this->vars[$key] = $val;
    }

    /**
     *
     * IssetVar
     * Vérifie si une variable existe
     * @param string $key Le nom de la variable
     * @return bool
     */
    public function issetVar($key)
    {
        return isset($this->vars[$key]);
    }

    /**
     * Injecter des variables
     *
     * Cette méthode permet d'injecter des variables extérieurs au templater
     *
     * @param array $vars
     */
    public function injectVars(array $vars){
        foreach($vars as $key => $val)
        {
            $val = preg_replace('#\{#', '&#x7B;', $val);
            $val = preg_replace('#\}#', '&#x7D;', $val);
            $this->setVar($key, $val);
        }
    }

    /**
     * Incrémente une variable int
     *
     * @param string $key Le nom de la variable
     * @param int $amount Le montant à augmenter
     */
    private function incrementIntVar($key, $amount = 1)
    {
        if(isset($this->vars[$key]))
        {
            $this->vars[$key] = (intval($this->vars[$key])+$amount);
        }
    }

    /**
     * Décrémente une variable int
     *
     * @param string $key Le nom de la variable
     * @param int $amount Le montant à diminuer
     */
    private function decrementIntVar($key, $amount = 1)
    {
        if(isset($this->vars[$key]))
        {
            $this->vars[$key] = (intval($this->vars[$key])-$amount);
        }
    }

    /**
     * Incrémente une variable string
     *
     * @param string $key Le nom de la variable
     * @param string $toAdd Le string à ajouter
     */
    private function incrementStringVar($key, $toAdd)
    {
        if(isset($this->vars[$key]))
        {
            $this->vars[$key] = $this->vars[$key] .= $toAdd;
        }
    }
}