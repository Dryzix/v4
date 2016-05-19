<?php
header('Content-Type: application/json');

$files = [];

$dir = opendir('./');
while(false !== ( $file = readdir($dir)) ) {
    if(is_file($file))
    {
        if(preg_match('#(.*).json$#', $file))
        {
            if($file != '__debug.json')
            {
                unlink($file);
            }
        }
    }
}

echo json_encode($files);


?>