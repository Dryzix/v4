<?php
    include 'head.php';
?>
<div class="col-md-2">
    <button type="button" id="resetBtn" class="btn btn-danger btn-block">Reset</button>
    <br />
    <ul class="list-group dashboad files">
    </ul>
</div>
<div class="col-md-10" id="fileinfos" data-displayed="0">
    <div class="jumbotron">
    	<div class="container">
    		<h1>Route : <span class="filename"></span></h1>
    		<p>Informations sur la route <span class="filename"></span> (Callable : <span class="callable"></span>)</p>
    		<p>Temps d'éxecution : <span class="time"></span> secondes</p>
            <hr />
    	</div>
    </div>
    <fieldset class="tree">
        <legend>Arborescence des templates</legend>
        <pre></pre>
    </fieldset>
    <fieldset class="sql">
        <legend>Requêtes SQL</legend>
        <div></div>
    </fieldset>
</div>
<?php
    include 'foot.php';
?>