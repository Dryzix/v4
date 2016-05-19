<?php
//$time_start = microtime(true);


include "../vendor/autoload.php";

use PHX\Core\Motor;

$test = new PHX\Core\Motor();
$confSitePath = realpath(__DIR__ . '/generic/conf/site.json');

$page = Motor::run($confSitePath);


//$time_end = microtime(true);
//$execution_time = ($time_end - $time_start);
//echo '<hr/><b>Total Execution Time:</b> '.$avg.' Sec<hr />';
echo $page;