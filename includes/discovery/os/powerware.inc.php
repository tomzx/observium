<?php

if (!$os)
{
  if (strstr($sysObjectId, ".1.3.6.1.4.1.534") || strstr($sysObjectId, ".1.3.6.1.4.1.705.1")) { $os = "powerware"; }
}

?>
