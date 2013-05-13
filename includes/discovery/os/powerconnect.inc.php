<?php

if (!$os)
{
  if (stristr($sysDescr, "PowerConnect ")) { $os = "powerconnect"; }
  else if (preg_match("/Dell.*Gigabit\ Ethernet/i",$sysDescr)) { $os = "powerconnect"; }
  else if (preg_match("/1\.3\.6\.1\.4\.1\.674\.10895\..*/",$sysObjectId))
  {
    if (stristr($sysDescr, "PowerConnect ")) { $os = "powerconnect"; }
    else if (preg_match("/^24G Ethernet Switch$/",$sysDescr)) { $os = "powerconnect"; }
    else if (preg_match("/^48G Ethernet Switch$/",$sysDescr)) { $os = "powerconnect"; }
    else if (preg_match("/^Ethernet Switch$/",$sysDescr)) { $os = "powerconnect"; }
    else if (preg_match("/^Ethernet Stackable Switching System$/",$sysDescr)) { $os = "powerconnect"; }
  }
}

?>
