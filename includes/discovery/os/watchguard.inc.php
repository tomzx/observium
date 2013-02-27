<?php

if (!$os)
{
  if (preg_match("/^WatchGuard\ Fireware/", $sysDescr) ||  preg_match("/^XTM/", $sysDescr)) { $os = "firebox"; }
}

?>
