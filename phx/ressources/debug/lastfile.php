<?php
if(isset($_GET['name']))
{
    $fp = fopen('lastfile.txt', "w");
    fwrite($fp, $_GET['name']);
    fclose($fp);
}
?>