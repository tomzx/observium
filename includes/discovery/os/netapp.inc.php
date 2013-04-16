<?php

if (!$os)
{
  // Detect filer and clustered filer as netapp. Unsure about netcache.
  if (strstr($sysObjectId, ".1.3.6.1.4.1.789.2.1"))       { $os = "netapp"; }
  if (strstr($sysObjectId, ".1.3.6.1.4.1.789.2.3"))       { $os = "netapp"; }

}

?>
