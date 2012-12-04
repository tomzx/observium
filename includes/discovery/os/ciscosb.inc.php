<?php

if (!$os)
{
  if (strstr($sysObjectId, ".1.3.6.1.4.1.9.6.1.83") || strstr($sysDescr, "10/100 8-Port VPN Router")) { $os = "ciscosb"; }
}

?>
