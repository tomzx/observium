<?php

if (!$os)
{
  if (strstr($sysDescr, "Cisco Controller")) { $os = "wlc"; }
}

?>