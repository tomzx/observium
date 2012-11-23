<?php

if (!$os)
{
  /// Changed to workaround bug in 8.x pixos returning "Cisco Cisco PIX" as sysDescr. #121
  if (preg_match("/^(Cisco\ )+PIX/", $sysDescr)) { $os = "pixos"; }
}

?>
