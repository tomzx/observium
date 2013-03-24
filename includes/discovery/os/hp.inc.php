<?php

if (!$os)
{

  // Legacy Procurve, H3C and new HP H3C detection. This should be all that's needed.

  if (strstr($sysObjectId, ".1.3.6.1.4.1.11.2.3.7.11")) { $os = "procurve"; }
  if (strstr($sysObjectId, ".1.3.6.1.4.1.2011"))        { $os = "h3c"; }

  if (strstr($sysObjectId, ".1.3.6.1.4.1.25506.1"))       { $os = "hh3c"; }

}

?>
