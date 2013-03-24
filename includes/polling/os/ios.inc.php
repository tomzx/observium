<?php

# 7200 and IOS-XE (ASR1k)
if (preg_match('/^Cisco IOS Software, .+? Software \([^\-]+-([^\-]+)-\w\),.+?Version ([^, ]+)/', $poll_device['sysDescr'], $regexp_result))
{
  $features = $regexp_result[1];
  $version = $regexp_result[2];
}

# 7600
elseif (preg_match('/Cisco Internetwork Operating System Software\s+IOS \(tm\) [^ ]+ Software \([^\-]+-([^\-]+)-\w\),.+?Version ([^, ]+)/', $poll_device['sysDescr'], $regexp_result))
{
  $features = $regexp_result[1];
  $version = $regexp_result[2];
}

# If we have not managed to match any IOS string yet (and that would be surprising)
# we can try to poll the Entity Mib to see what's inside
else
{
  $oids = "entPhysicalModelName.1 entPhysicalContainedIn.1 entPhysicalName.1 entPhysicalSoftwareRev.1 entPhysicalModelName.1001 entPhysicalContainedIn.1001 cardDescr.1 cardSlotNumber.1";

  $data = snmp_get_multi($device, $oids, "-OQUs", "ENTITY-MIB:OLD-CISCO-CHASSIS-MIB");

  if ($data[1]['entPhysicalContainedIn'] == "0")
  {
    if (!empty($data[1]['entPhysicalSoftwareRev']))
    {
     $version = $data[1]['entPhysicalSoftwareRev'];
    }
    if (!empty($data[1]['entPhysicalName']))
    {
      $hardware = $data[1]['entPhysicalName'];
    }
    if (!empty($data[1]['entPhysicalModelName']))
    {
      $hardware = $data[1]['entPhysicalModelName'];
    }
  }
}

if(empty($hardware)) {   $hardware = snmp_get($device, "sysObjectID.0", "-Osqv", "SNMPv2-MIB:CISCO-PRODUCTS-MIB"); }

?>
