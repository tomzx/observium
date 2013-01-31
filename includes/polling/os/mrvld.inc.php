<?php

  $hardware = trim(snmp_get($device, "oaLdCardBackplanePN.0", "-OQv", "OADWDM-MIB"),'"');

  $serial = trim(snmp_get($device, "oaLdCardBackplaneSN.0", "-OQv", "OADWDM-MIB"),'"');

  $version = trim(snmp_get($device, "oaLdSoftVersString.0", "-OQv", "OADWDM-MIB"),'"');
  #version is a null termianted hex-string, convert to normal string and strip null bytes
  $version = trim(snmp_hexstring($version),"\x00");

#  $features =

?>
