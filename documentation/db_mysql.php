<style>
    .tabledb td,th {
        text-align: center;
    }
</style>
<div class="jumbotron">
    <div class="container">
        <h1>Base de données - Mysql</h1>
        <p>
            Contient toutes les fonctions basiques du moteur de template concernant les tags de base de donnée en utilisant une base de donnée type Mysql<br />
            Ci-dessous, nous considérerons que nous utilisons la base de donnée suivante, et les jeux d'éssai suivants :
        </p>
            <br /><br />
        <div class="col-md-4">
            <img src="img/db/screenshot1.PNG" alt="concepteur">
            </div>
        <div class="col-md-4">
            <table class="table tabledb table-striped table-hover">
                <thead>
                <tr>
                    <th colspan="2">Table classe</th>
                </tr>
                <tr>
                    <th>id</th>
                    <th>libelle</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Seconde</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Première</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Terminale</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <table class="table tabledb table-striped table-hover">
                <thead>
                <tr>
                    <th colspan="3">Table eleve</th>
                </tr>
                <tr>
                    <th>id</th>
                    <th>nom</th>
                    <th>idClasse</th>
                </tr>
                </thead>
                <tbody>
                    <tr><td>1</td><td>Jean</td><td>1</td></tr>
                    <tr><td>2</td><td>Marc</td><td>1</td></tr>
                    <tr><td>3</td><td>Pierre</td><td>1</td></tr>
                    <tr><td>4</td><td>Maurice</td><td>3</td></tr>
                    <tr><td>5</td><td>Olivier</td><td>2</td></tr>
                    <tr><td>6</td><td>Florent</td><td>1</td></tr>
                    <tr><td>7</td><td>Florian</td><td>1</td></tr>
                    <tr><td>8</td><td>Marie</td><td>2</td></tr>
                    <tr><td>9</td><td>Mélissa</td><td>2</td></tr>
                    <tr><td>10</td><td>Marine</td><td>3</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-md-10 col-md-offset-1">
    <h1 id="rand" class="page-header">Tag dbSelect </h1>
    <p>
        Ce tag sert à executer une requête select dans la base de donnée (Ce tag peut être utilisé de manière inline)
    </p>
        <table class="table table-striped table-hover">
        	<thead>
        		<tr>
        			<th style="width: 200px;">Paramètre</th>
        			<th>Valeur</th>
        		</tr>
        	</thead>
        	<tbody>
        		<tr>
        			<td>in (Obligatoire)</td>
        			<td>Correspond à la clef qui va permettre d'identifier cette requête afin de l'utiliser dans un dbLoop</td>
        		</tr>
                <tr>
                    <td>tables (ou t) (Obligatoire)</td>
                    <td>Tableau contenant toutes les tables et jointures de la requête</td>
                </tr>
                <tr>
                    <td>columns (ou c)</td>
                    <td>Tableau contenant toutes les colonnes de la requête (dans un SELECT)</td>
                </tr>
                <tr>
                    <td>where (ou w)</td>
                    <td>Tableau contenant toutes les conditions de la requête (dans un WHERE)</td>
                </tr>
                <tr>
                    <td>group (ou g)</td>
                    <td>Tableau contenant ce qui doit ce situer dans le GROUP BY</td>
                </tr>
                <tr>
                    <td>having (ou h)</td>
                    <td>Tableau contenant ce qui doit ce situer dans le HAVING</td>
                </tr>
                <tr>
                    <td>order (ou o)</td>
                    <td>Tableau contenant ce qui doit ce situer dans le ORDER BY</td>
                </tr>
                <tr>
                    <td>limit (ou l)</td>
                    <td>Tableau contenant ce qui doit ce situer dans le LIMIT</td>
                </tr>
                <tr>
                    <td>values (ou v)</td>
                    <td>Tableau contenant les valeurs qui vont être injectés dans le execute() de PDO</td>
                </tr>
        	</tbody>
        </table>
    <br />
    <h1 id="rand" class="page-header">Tag dbLoop </h1>
    <p>
        Ce permet de boucler sur les différents résultats renvoyés par une requête dbSelect
    </p>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th style="width: 200px;">Paramètre</th>
            <th>Valeur</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>for (Obligatoire)</td>
            <td>Correspond à la clef afin d'identifier la requête sur laquelle bouclé (précédemment créé grâce à un dbSelect)</td>
        </tr>
        </tbody>
    </table>
    <br />
    <h3>Exemples d'utilisation :</h3>
    <br />
    <h4>Exemple 1 :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{t=[
    "c" -> "classe",
    "j1" -> ["inner" -> "c", "e"->"eleve", "c.id = e.idClasse"]
    ]
}}
{dbSelect in="var" t={{t}} c="c.id, e.id"  /}
{dbLoop for="var"}
    Id eleve : {var[e]->id}<br />
    Id classe : {var[c]->id}<br />
    -----------------------------<br/>
{/dbLoop}');
            ?></code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">SELECT c.id AS PHXal_c_id, e.id AS PHXal_e_id FROM `classe` c INNER JOIN `eleve` e ON c.id = e.idClasse</code></pre>
    <h4>Retournera :</h4>
    <pre>Id eleve : 1
Id classe : 1
-----------------------------
Id eleve : 2
Id classe : 1
-----------------------------
Id eleve : 3
Id classe : 1
-----------------------------
Id eleve : 6
Id classe : 1
-----------------------------
Id eleve : 7
Id classe : 1
-----------------------------
Id eleve : 5
Id classe : 2
-----------------------------
Id eleve : 8
Id classe : 2
-----------------------------
Id eleve : 9
Id classe : 2
-----------------------------
Id eleve : 4
Id classe : 3
-----------------------------
Id eleve : 10
Id classe : 3
-----------------------------</pre>
    <br />
    <h4>Avec un order (et alias) :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{t=[
    "c" -> "classe",
    "j1" -> ["inner" -> "c", "e"->"eleve", "c.id = e.idClasse"]
    ]
}}
{dbSelect in="var" t={{t}} c="c.id, e.id as idEleve" o="e.id"  /}
{dbLoop for="var"}
    Id eleve : {var->idEleve}<br />
    Id classe : {var->id}<br />
    -----------------------------<br/>
{/dbLoop}');
            ?></code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">SELECT c.id AS PHXal_id, e.id AS PHXal_idEleve FROM `classe` c INNER JOIN `eleve` e ON c.id = e.idClasse ORDER BY e.id</code></pre>
    <h4>Retournera :</h4>
    <pre>Id eleve : 1
Id classe : 1
-----------------------------
Id eleve : 2
Id classe : 1
-----------------------------
Id eleve : 3
Id classe : 1
-----------------------------
Id eleve : 4
Id classe : 3
-----------------------------
Id eleve : 5
Id classe : 2
-----------------------------
Id eleve : 6
Id classe : 1
-----------------------------
Id eleve : 7
Id classe : 1
-----------------------------
Id eleve : 8
Id classe : 2
-----------------------------
Id eleve : 9
Id classe : 2
-----------------------------
Id eleve : 10
Id classe : 3
-----------------------------</pre>
    <br />
    <h4>Avec une fonction d'agrégation :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{t=[
        "c" -> "classe",
        "j1" -> ["inner" -> "c", "e"->"eleve", "c.id = e.idClasse"]
    ]
}}
{dbSelect in="var" t={{t}} c="c.libelle, COUNT(*)" g="1" /}
{dbLoop for="var"}
    Nom de la classe : {var->libelle}<br />
    Nombre d\'élèves : {var->count}<br /><br />
{/dbLoop}');
            ?></code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">SELECT c.libelle AS PHXal_libelle, COUNT(*) AS PHXal_count FROM `classe` c INNER JOIN `eleve` e ON c.id = e.idClasse GROUP BY 1</code></pre>

    <h4>Retournera :</h4>
    <pre>Nom de la classe : Première
Nombre d'élèves : 3

Nom de la classe : Seconde
Nombre d'élèves : 5

Nom de la classe : Terminale
Nombre d'élèves : 2</pre>
    <br />
    <h3>Passage de valeur(s) :</h3>
    <p><span style="font-weight: bold; color: #af0006;">Attention toutes les valeurs doivent être passé de cette façon afin d'être sécurisés</span><br />
    Nous allons utiliser le paramètre values (ou v) du tag dbSelect pour passer des paramètres à une requête</p>
    <h4>Exemple 1 :</h4>
    <p>Ici l'appel a la variable {{classe}} se fait entre quote afin d'éviter les espaces dans la requête, sans les quotes il faut coller la variable a la flêche (Exemple : 'libC' ->{{classe}})</p>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{t=[
        "c" -> "classe",
        "j1" -> ["inner" -> "c", "e"->"eleve", "c.id = e.idClasse"]
    ]
}}
{{classe="Terminale"}}
{dbSelect in="var" t={{t}} c="c.libelle, COUNT(*)" w="libelle=:libC" v=[\'libC\' -> \'{{classe}}\'] /}
{dbLoop for="var"}
    Nom de la classe : {var->libelle}<br />
    Nombre d\'élèves : {var->count}<br /><br />
{/dbLoop}');
            ?></code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">SELECT c.libelle AS PHXal_libelle, COUNT(*) AS PHXal_count FROM `classe` c INNER JOIN `eleve` e ON c.id = e.idClasse WHERE libelle='Terminale'</code></pre>

    <h4>Retournera :</h4>
    <pre>
Nom de la classe : Terminale
Nombre d'élèves : 2</pre>
    <br />
    <h4>Exemple 2 :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{t=[
        "c" -> "classe",
        "j1" -> ["inner" -> "c", "e"->"eleve", "c.id = e.idClasse"]
    ]
}}
{{classe="Terminale"}}
{dbSelect in="var" t={{t}} c="c.libelle, COUNT(*)" w="libelle=?" v=[{{classe}}] /}
{dbLoop for="var"}
    Nom de la classe : {var->libelle}<br />
    Nombre d\'élèves : {var->count}<br /><br />
{/dbLoop}');
            ?></code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">SELECT c.libelle AS PHXal_libelle, COUNT(*) AS PHXal_count FROM `classe` c INNER JOIN `eleve` e ON c.id = e.idClasse WHERE libelle='Terminale'</code></pre>

    <h4>Retournera :</h4>
    <pre>
Nom de la classe : Terminale
Nombre d'élèves : 2</pre>
<br />
    <h1 id="rand" class="page-header">Tag dbInsert</h1>
    <p>
        Ce permet d'ajouter un enregistrement sur une table de la base de données
    </p>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th style="width: 200px;">Paramètre</th>
            <th>Valeur</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>table (ou t) (Obligatoire)</td>
                <td>La table dans laquelle on souhaite insérer le nouvel enregistrement</td>
            </tr>
            <tr>
                <td>values (ou v) (Obligatoire)</td>
                <td>Les valeurs à insérer dans la base de données</td>
            </tr>
            <tr>
                <td>columns (ou c)</td>
                <td>Les champs que l'on souhaite insérer</td>
            </tr>
        </tbody>
    </table>
    <br />
    <h4>Exemple (sans columns) :</h4>
    <pre><code class="language-smarty">{{t='eleve'}}
{{v=['id'->'', 'nom' -> 'Guy', 'idClasse' -> '2']}}
{dbInsert t={{t}}  v={{v}}/}</code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">INSERT INTO eleve  VALUES ( '', 'Guy', '2' )</code></pre>
    <br />
    <h4>Exemple (avec columns et sans alias dans values) :</h4>
    <pre><code class="language-smarty">{{t='eleve'}}
{{c='nom, idClasse'}}
{{v=['Guy', '2']}}
{dbInsert t={{t}} c={{c}}  v={{v}}/}</code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">INSERT INTO eleve (nom, idClasse) VALUES ( 'Guy', '2' )</code></pre>
    <br />

    <h1 id="rand" class="page-header">Tag dbUpdate</h1>
    <p>
        Ce tag permet de mettre à jour un enregistrement sur une table de la base de données
    </p>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th style="width: 200px;">Paramètre</th>
            <th>Valeur</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>table (ou t) (Obligatoire)</td>
            <td>La table dans laquelle on souhaite mettre à jour l'enregistrement</td>
        </tr>
        <tr>
            <td>values (ou v) (Obligatoire)</td>
            <td>Les valeurs de la mise à jour</td>
        </tr>
        <tr>
            <td>set (ou s) (Obligatoire)</td>
            <td>Les champs que l'on souhaite mettre à jour</td>
        </tr>
        <tr>
            <td>where (ou w)</td>
            <td>Les conditions de la mise à jour</td>
        </tr>
        </tbody>
    </table>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty">{{t='eleve'}}
{{s='nom=?'}}
{{v=['Jacques',11]}}
{{w='id=?'}}
{dbUpdate t={{t}} s={{s}} v={{v}} w={{w}} /}</code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">UPDATE eleve SET nom='Jacques' WHERE id='11'</code></pre>
    <br />
    <h4>Exemple (avec alias) :</h4>
    <pre><code class="language-smarty">{{t='eleve'}}
{{s='nom=:nom'}}
{{v=['nom'->'Jacques', 'id'->11]}}
{{w='id=:id'}}
{dbUpdate t={{t}} s={{s}} v={{v}} w={{w}} /}</code></pre>
    <h4>Requête générée :</h4>
    <pre><code class="language-sql">UPDATE eleve SET nom='Jacques' WHERE id='11'</code></pre>
<br />

</div>