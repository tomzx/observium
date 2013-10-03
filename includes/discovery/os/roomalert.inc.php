<?php

if (!$os)
{
  if (preg_match("/^RoomAlert/", $sysDescr)) { $os = "roomalert"; }
}

?>