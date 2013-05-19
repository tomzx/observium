<?php

if (!$os)
{
  if (strstr($sysObjectId, ".1.3.6.1.4.1.24062.2.1") ||
      strstr($sysObjectId, ".1.3.6.1.4.1.24062.2.2") ||
      strstr($sysObjectId, ".1.3.6.1.4.1.24062.2.3") ) { $os = "korenix-jetnet"; }
}

?>
