<?php

if (empty($os))
{
  if (strstr($sysDescr, "Cisco Internetwork Operating System Software")) { $os = "ios"; }
  else if (strstr($sysDescr, "IOS (tm)")) { $os = "ios"; }
  else if (strstr($sysDescr, "Cisco IOS Software")) { $os = "ios"; }
  else if (strstr($sysDescr, "Global Site Selector")) { $os = "ios"; }

  if (strstr($sysDescr, "IOS-XE")) { $os = "iosxe"; }
  if (strstr($sysDescr, "IOS XR")) { $os = "iosxr"; }

  if (strstr($sysDescr, "/Cisco Service Control/")) { $os = "ciscoscos"; }

}

# Fallback case
# If we don't have an OS yet and if the object is in Cisco tree
//if (empty($os) && strpos($sysObjectId, '.1.3.6.1.4.1.9.1.') === 0)
//{
//  $cos = str_replace('.1.3.6.1.4.1.9.1.', '', $sysDescr);
//  if (is_numeric($cos))
//  {
//    $os = "ios";
//  }
//}

?>
