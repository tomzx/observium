<?php

if (!$os)
{
  if (strpos($sysDescr, "Cisco Adaptive Security Appliance") !==  false) { $os = "asa"; }
}

?>
