<?php

if (!$os)
{
  if (strstr($sysDescr, "Force10 Operating System")) { $os = "ftos"; }
  if (strstr($sysDescr, "Force10 OS")) { $os = "ftos"; }

}

?>
