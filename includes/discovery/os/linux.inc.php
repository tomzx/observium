<?php

if (!$os)
{

  // First check the sysObjectID, then the sysDescr
  if (strstr($sysObjectId, "1.3.6.1.4.1.8072.3.2.10"))
  {
    $os = "linux";
  } elseif (preg_match("/^Linux/", $sysDescr)) {
    $os = "linux";
  }

  // Specific Linux-derivatives

  if ($os == "linux")
  {
    // Check for QNAP Systems TurboNAS
    $entPhysicalMfgName = snmp_get($device, "ENTITY-MIB::entPhysicalMfgName.1", "-Osqnv");

    // Check for devices based on Linux
    if (strstr($sysObjectId, ".1.3.6.1.4.1.5528.100.20.10.2014")) { $os = "netbotz"; }
    elseif (strstr($sysDescr, "endian")) { $os = "endian"; }
    elseif (preg_match("/Cisco Small Business/", $sysDescr)) { $os = "ciscosmblinux"; }
    elseif (strpos($entPhysicalMfgName, "QNAP") !== FALSE) { $os = "qnap"; }
    elseif (strpos($sysObjectId, ".1.3.6.1.4.1.3375.2.1.3.4.") !== FALSE) { $os = "f5"; }
    elseif (is_numeric(trim(snmp_get($device,"roomTemp.0", "-OqvU", "CAREL-ug40cdz-MIB")))) { $os = "pcoweb"; }
    elseif (strpos(trim(snmp_get($device, "hrSystemInitialLoadParameters.0", "-Osqnv")), "syno_hw_version") !== FALSE) { $os = "dsm"; }
    elseif (strpos(trim(snmp_get($device, "dot11manufacturerName.5", "-Osqnv", "IEEE802dot11-MIB")), "Ubiquiti") !== FALSE) { $os = "airos"; }
    elseif (preg_match("/^SecurePlatform/", snmp_get($device, "1.3.6.1.4.1.2620.1.6.5.1.0", "-Oqv"))) { $os = "splat"; }
  }
}

?>
