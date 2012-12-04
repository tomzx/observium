<?php

if (!$os)
{
  if (preg_match("/DrayTek/i", $sysDescr)) { $os = "draytek"; }
}

?>

