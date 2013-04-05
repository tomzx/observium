<?php

$hardware = snmp_get($device, "sysObjectID.0", "-OQsv", "FOUNDRY-SN-AGENT-MIB:FOUNDRY-SN-ROOT-MIB", mib_dirs("foundry"));
$hardware = rewrite_ironware_hardware($hardware);

$version = snmp_get($device, "snAgBuildVer.0", "-OQsv", "FOUNDRY-SN-AGENT-MIB:FOUNDRY-SN-ROOT-MIB", mib_dirs("foundry"));
$version = str_replace(array('V', '"'), '', $version);

$serial = snmp_get($device, "snChasSerNum.0", "-OQsv", "FOUNDRY-SN-AGENT-MIB:FOUNDRY-SN-ROOT-MIB", mib_dirs("foundry"));

?>
