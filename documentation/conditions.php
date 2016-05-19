<div class="jumbotron">
    <div class="container">
        <h1>Les conditions</h1>
        <p>Contient toutes les types de conditions du moteur de template</p>
    </div>
</div>
<div class="col-md-10 col-md-offset-1">
    <h1 id="rand" class="page-header">If </h1>
    <p>Cette condition permet d'executer une condition if en fonction des paramètres demandés</p>
    <table class="table table-striped table-hover">
    	<thead>
    		<tr>
    			<th>Paramètre</th>
    			<th>Valeur</th>
    		</tr>
    	</thead>
    	<tbody>
    		<tr>
    			<td>cond</td>
    			<td>Correspond au nom de la condition demandé</td>
    		</tr>
            <tr>
                <td>param(s)</td>
                <td>Correspond au(x) paramètre(s) de la condition demandé</td>
            </tr>
            <tr>
                <td>ccond</td>
                <td>"Complex condition" permet de faire des conditions complèxe avec and/or et des parenthèses pour les priorités</td>
            </tr>
    	</tbody>
    </table>
    <p>
    <h4>Exemple 1 :</h4>
    <pre><code class="language-smarty line-numbers"><?php echo
            htmlspecialchars("{{var='non_vide'}}
{if cond=\"notEmpty\" param={{var}}}
    var n'est pas vide
{/if}");
?></code></pre>
    <h4>Retournera :</h4>
    <pre><?php echo "var n'est pas vide" ?></pre>
    <br />
    <h4>Exemple 2 :</h4>
    <pre><code class="language-php line-numbers"><?php echo
            htmlspecialchars("{{var=\"\"}} {{bool=false}}
{if ccond=\"(notEmpty(\"+{{var}}+\") or isTrue(true)) and isFalse(\"+{{bool}}+\")\"}
    Condition vrai
{/if}");
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre><?php echo "Condition vrai" ?></pre>
    </p>
    <br />
    <h4>Les conditions existantes :</h4>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Condition</th>
            <th>Effet</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>empty</td>
            <td>Renvoi true si la valeur envoyé est vide</td>
        </tr>
        <tr>
            <td>notEmpty</td>
            <td>Renvoi true si la valeur envoyé n'est pas vide</td>
        </tr>
        <tr>
            <td>isTrue</td>
            <td>Renvoi true si la valeur envoyé est "true" ou "1"</td>
        </tr>
        <tr>
            <td>isFalse</td>
            <td>Renvoi true si la valeur envoyé est "false" ou "0"</td>
        </tr>
        </tbody>
    </table>
    <br />
    <h1 id="rand" class="page-header">Elseif </h1>
    <p>Cette condition est exécuté si le précédent {if/elseif} a renvoyé faux</p>
    <h4>Exemple 1 :</h4>
    <pre><code class="language-smarty line-numbers"><?php echo
            htmlspecialchars("{if ccond=\"isTrue(false)\"}
    Nous somme dans le if
{/if}
{elseif ccond=\"isFalse(false)\"}
    Nous somme dans le elseif
{/elseif}
{else}
    Nous somme dans le else
{/else}");
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre><?php echo
        htmlspecialchars("Nous somme dans le elseif");
        ?></pre>
    <br />
    <h1 id="rand" class="page-header">Else </h1>
    <p>Cette condition est exécuté si le précédent {if/elseif} a renvoyé faux</p>
    <h4>Exemple 1 :</h4>
    <pre><code class="language-smarty line-numbers"><?php echo
            htmlspecialchars("{if ccond=\"isTrue(false)\"}
    Nous somme dans le if
{/if}
{else}
    Nous somme dans le else
{/else}");
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre><?php echo
            htmlspecialchars("Nous somme dans le else");
            ?></pre>
</div>