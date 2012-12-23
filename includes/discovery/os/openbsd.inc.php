<?php

if (!$os)
{
  if ($sysObjectId == ".1.3.6.1.4.1.30155.23.1") { $os = "openbsd"; }
  if ($sysObjectId == ".1.3.6.1.4.1.8072.3.2.12") { $os = "openbsd"; }  // Net-SNMPd
}

?>
