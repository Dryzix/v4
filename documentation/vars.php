<div class="jumbotron">
	<div class="container">
		<h1>Variables</h1>
		<p>Contient toutes les informations sur les variables du moteur de template</p>
	</div>
</div>
<div class="col-md-10 col-md-offset-1">
    <h1 id="rand" class="page-header">Déclaration </h1>
    <p>Une variable se déclare simplement en utilisant des doubles crochets et le signe "=" (Caractères autorisés : Lettres (majuscules et minuscules), chiffres et underscore ( _ )</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
htmlspecialchars('{{variable="Contenu de la variable"}}');
            ?></code></pre>
    </p>

    <h1 id="rand" class="page-header">Affichage </h1>
    <p>Pour afficher une variable il suffit de place son nom entre doubles crochets (dans cet exemple nous reprendrons la variable déclaré ci-dessus</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{variable}}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>Contenu de la variable</pre>
    </p>

    <h1 id="rand" class="page-header">Opérateur "++" </h1>
    <p>Pour une variable numérique, l'opérateur ++ permet d'incrémenter cette variable de 1 (Attention un string sera considéré comme un 0)</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{nb=1}}
{{nb++}}
{{nb}}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>2</pre>
    </p>

    <h1 id="rand" class="page-header">Opérateur "--" </h1>
    <p>Pour une variable numérique, l'opérateur -- permet de décrémenté cette variable de 1 (Attention un string sera considéré comme un 0)</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{nb=1}}
{{nb--}}
{{nb}}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>0</pre>
    </p>

    <h1 id="rand" class="page-header">Opérateur "+=" </h1>
    <p>Pour une variable numérique, l'opérateur += permet d'incrémenter cette variable du montant spécifié en paramètre (Attention un string sera considéré comme un 0)</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{nb=1}}
{{nb+=9}}
{{nb}}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>10</pre>
    </p>

    <h1 id="rand" class="page-header">Opérateur "-=" </h1>
    <p>Pour une variable numérique, l'opérateur -= permet de décrémenter cette variable du montant spécifié en paramètre (Attention un string sera considéré comme un 0)</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{nb=10}}
{{nb-=9}}
{{nb}}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>1</pre>
    </p>

    <h1 id="rand" class="page-header">Opérateur ".=" </h1>
    <p>Pour une variable de type string, l'opérateur .= permet d'ajouter à la fin de celle-ci le texte spécifié en paramètre</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{var="Hello"}}
{{var.=" World"}}
{{var}}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>Hello World</pre>
    </p>

    <h1 id="rand" class="page-header">Tableaux</h1>
    <p>Les tableaux sont autorisés dans le moteur de template, l'intérêt ici est principalement de pouvoir les passer en paramètres à des tags</p>
    <h4>Exemple:</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars('{{tableau=["t",1,[1,2],3]}}
{{tableau}}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>["t",1,[1,2],3]</pre>
</div>