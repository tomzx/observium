<?php

if (!$os)
{

  // First check the sysObjectID, then the sysDescr
  if (strstr($sysObjectId, "1.3.6.1.4.1.8072.3.2.8"))
  {
    $os = "freebsd";
  } elseif (strstr($sysDescr, 'FreeBSD'))
  {
    $os = "freebsd";
  } elseif (strstr($sysDescr, "m0n0wall"))
  {
    $os = "monowall"; // m0n0wall, based on FreeBSD
  }
}

?>
