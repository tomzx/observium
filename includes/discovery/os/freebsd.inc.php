<?php

if (!$os)
{
  if (strstr($sysDescr, "FreeBSD")) { $os = "freebsd"; }    // It's FreeBSD!
  if (strstr($sysDescr, "m0n0wall")) { $os = "monowall"; }  // Ditto
}

?>
