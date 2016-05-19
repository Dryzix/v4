<div class="jumbotron">
    <div class="container">
        <h1>Les conditions</h1>
        <p>Contient toutes les types de conditions du moteur de template</p>
    </div>
</div>
<div class="col-md-10 col-md-offset-1">
    <h1 id="rand" class="page-header">Loop </h1>
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
    			<td>iterations (ou i)</td>
    			<td>Correspond au nombre d'itérations de la boucle</td>
    		</tr>
    	</tbody>
    </table>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
            htmlspecialchars("{loop i=5}
Salut
{/loop}");
?></code></pre>
    <h4>Retournera :</h4>
    <pre>Salut
Salut
Salut
Salut
Salut</pre>
    </p>
</div>