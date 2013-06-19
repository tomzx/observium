<?php

if (preg_match("/^EdgeOS/", $poll_device['sysDescr'])) 
{ 
  $version = $poll_device['sysDescr'];
  $version = preg_replace("/^EdgeOS/", "", $version);
}

?>
