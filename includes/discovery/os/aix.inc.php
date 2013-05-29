<?php

if (!$os)
{
  if (strstr($sysObjectId, "1.3.6.1.4.1.2.3.1.2.1.1.2") ||
      strstr($sysObjectId, "1.3.6.1.4.1.2.3.1.2.1.1.3") ) { $os = "aix"; }
}

?>
