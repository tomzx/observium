<?php

if (!$os)
{
  if (strstr($sysObjectId, "1.3.6.1.4.1.4115.1.8.1")) { $os = "arris-d5"; }
  if (strstr($sysObjectId, "1.3.6.1.4.1.4115.1.4.3")) { $os = "arris-c3"; }
}

?>
