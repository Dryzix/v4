<div class="jumbotron">
    <div class="container">
        <h1>Mise en place et installation</h1>
        <p>Ce document contient les informations nécessaires à la mise en place d'un nouveau projet en utilisant la version 4 du moteur
            PHX</p>
    </div>
</div>
<div class="col-md-10 col-md-offset-1">
    <h1 id="rand" class="page-header">Structure</h1>
    <p>Après la création de votre nouveau dossier contenant le futur projet, vous devez y insérer le dossier contenant le moteur</p>
    <img src="img/install/screenshot1.png" alt="importation" title="Importation du dossier PHX">
    <br /><br />
    <p>Une fois cette étape passé, vous devez créer le dossier contenant les parties spécifique du nouveau projet, ici on l'appelera "site"</p>
    <img src="img/install/screenshot2.png" alt="creation_dossier" title="Création du dossier site">
    <br /><br />
    <p>Maintenant, toujours à la racine de votre projet, initialisez composer et renseignez les différents champs demandés</p>
    <img src="img/install/screenshot3.png" alt="composer_init">
    <br /><br />
    <p>Votre structure est maintenant terminé et doit ressembler à l'image ci-dessous</p>
    <img src="img/install/screenshot4.png" alt="structure">

    <h1 id="rand" class="page-header">Configuration</h1>
    <p>Vous devez maintenant créer vos différents fichiers de configuration, commencons par le fichier de configuration global du site que nous appelerons ici site.json, placé dans /site/conf/<br />
    Il est important de savoir que ce fichier peut être rangé à l'endroit que vous souhaitez, cela n'aura aucun impact sur le fonctionnement du moteur.</p>
    <img src="img/install/screenshot5.png" alt="site_json">
    <br /><br />
    <p>Nous allons maintenant placer l'ensemble des configurations minimales au bon fonctionnement du moteur que voici :</p>
    <pre><code class="language-json"><?php echo
            htmlspecialchars('{
    "rootPath": "C:/xampp/htdocs/nouveau_site/site/",
    "confPath": "conf/",
    "Cache": {
      "use": true,
      "path": "tmp"
    },
    "Templater":{
    "tagsNamespace" : "\\SITE\\Motor\\Tag",
    "tags":{
      "custom" : ["perso"]
    }
    },
    "Router":{
    "routesConfFile": "routes.json"
    },
    "Controller":{
    "controllersNamespace" : "\\SITE\\Controller"
    }
}');
            ?></code></pre>
    <br /><br />
</div>