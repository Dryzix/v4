<div class="jumbotron">
	<div class="container">
		<h1>Fonctions basiques</h1>
		<p>Contient toutes les fonctions basiques du moteur de template</p>
	</div>
</div>
<div class="col-md-10 col-md-offset-1">
    <h1 id="rand" class="page-header">Rand </h1>
    <p>Cette fonction retourne un nombre aléatoire</p>
    <p>
    <h4>Exemple :</h4>
    <pre><code class="language-smarty"><?php echo
htmlspecialchars('{rand}0|10{/rand}');
            ?></code></pre>
    <h4>Retournera :</h4>
    <pre>3</pre>
    </p>
</div>