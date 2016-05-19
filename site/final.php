<?php
function randstr(){
  $str = 'azertyuiopqsdfghjklmwxcvbn';
  $ret = '';
  for($i=0;$i<7;++$i)
  {
    $ret .= $str[rand(0,25)];
  }

  return $ret;
}

for($i=0;$i<5000;++$i)
{
  $varname = randstr();
  echo '{{' . $varname . '="' . randstr() . '"}} {{' . $varname . '.="' . randstr() . '"}}' . htmlspecialchars('<br/>');
}
 ?>
