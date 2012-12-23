<?php

if (strpos($poll_device['sysDescr'], "olive"))
{
  $hardware = "Olive";
  $serial = "";
}
else
{
  $hardware = snmp_get($device, "sysObjectID.0", "-Ovqsn");
  $hardware = "Juniper " . rewrite_junos_hardware($hardware);
  $junose_version   = snmp_get($device, "juniSystemSwVersion.0", "-Ovqs", "+Juniper-System-MIB", $config['install_dir']."/mibs/junose");
  $junose_serial    = "";
}

list($version) = explode(" ", $junose_version);
list(,$version) =  explode("(", $version);
list($features) = explode("]", $junose_version);
list(,$features) =  explode("[", $features);

?>
