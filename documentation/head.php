<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/prism.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Documentation</title>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Home</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="<?php echo $page == "home" ? 'active' : '' ?>"><a href="index.php">Home</a></li>
            <li class="<?php echo $page == "vars" ? 'active' : '' ?>"><a href="index.php?page=vars">Variables</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Contrôles <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li class="<?php echo $page == "conditions" ? 'active' : '' ?>"><a href="index.php?page=conditions">Conditions</a></li>
                    <li class="<?php echo $page == "loops" ? 'active' : '' ?>"><a href="index.php?page=loops">Boucles</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Fonctions <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li class="<?php echo $page == "func_basic" ? 'active' : '' ?>"><a href="index.php?page=func_basic">Basiques</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Bases de données <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li class="<?php echo $page == "db_mysql" ? 'active' : '' ?>"><a href="index.php?page=db_mysql">Avec Mysql</a></li>
                </ul>
            </li>
        </ul>
        <form class="navbar-form navbar-left" role="search">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Search">
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="index.php?page=installation">Installation&nbsp;&nbsp;</a></li>
        </ul>
    </div><!-- /.navbar-collapse -->
</nav>
