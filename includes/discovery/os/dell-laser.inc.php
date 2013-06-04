<?php

if (!$os)
{
  if (strstr($sysObjectId, "1.3.6.1.4.1.674.10898.2.100.10")) { $os = "dell-laser"; }
  elseif (strstr($sysDescr, "Dell Color Laser")) { $os = "dell-laser"; }
  elseif (strstr($sysDescr, "Dell Laser Printer")) { $os = "dell-laser"; }
  elseif (preg_match("/^Dell.*MFP/", $sysDescr)) { $os = "dell-laser"; }

}

?>
