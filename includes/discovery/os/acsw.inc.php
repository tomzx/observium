<?php

if (!$os)
{
  if (strstr($sysDescr, "Cisco Application Control Software")) { $os = "acsw"; }
  else if (strstr($sysDescr, "Application Control Engine")) { $os = "acsw"; }
  else if (strstr($sysDescr, "ACE")) { $os = "acsw"; }

}

?>
