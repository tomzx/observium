<?php

/// HP / H3C Comware

if (!$os)
{
  if (strstr($sysDescr, "Comware")) { $os = "comware"; }
  if (strstr($sysObjectId, ".1.3.6.1.4.1.25506")) { $os = "comware"; }
}

?>
