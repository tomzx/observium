<?php


$oids = "entPhysicalModelName.1 entPhysicalSoftwareRev.1 entPhysicalSerialNum.1";
	  

$data = snmp_get_multi($device, $oids, "-OQUs", "ENTITY-MIB");

  if (isset($data[1]['entPhysicalSoftwareRev']) && $data[1]['entPhysicalSoftwareRev'] != "")
  {
    $version = $data[1]['entPhysicalSoftwareRev'];
  }
  if (isset($data[1]['entPhysicalName']) && $data[1]['entPhysicalName'] != "")
  {
    $hardware = $data[1]['entPhysicalName'];
  }
  if (isset($data[1]['entPhysicalModelName']) && $data[1]['entPhysicalModelName'] != "")
  {
    $hardware = $data[1]['entPhysicalModelName'];
  }

  if(empty($hardware)) {   $hardware = snmp_get($device, "sysObjectID.0", "-Osqv", "SNMPv2-MIB:CISCO-PRODUCTS-MIB"); }

?>
