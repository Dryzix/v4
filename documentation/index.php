<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
include "head.php";


$allowedPages = ['home', 'func_basic', 'conditions', 'loops', 'vars', 'installation', 'db_mysql'];

if(in_array($page, $allowedPages)):
	include $page . '.php';
endif;

include "foot.php";
?>