<?php

if (!$os)
{
  if (strstr($sysObjectId, ".1.3.6.1.4.1.12740.17.1")) { $os = "equallogic"; }
  if (strstr($sysObjectId, ".1.3.6.1.4.1.12740.12.1.1.0")) { $os = "equallogic"; }
}

?>
