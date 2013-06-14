<?php

$version = trim(snmp_get($device, "upsIdentUPSSoftwareVersion.0", "-OQv", "UPS-MIB"),'"');

$manufacturer = trim(snmp_get($device, "upsIdentManufacturer.0", "-OQv", "UPS-MIB"),'"');
$model = trim(snmp_get($device, "upsIdentModel.0", "-OQv", "UPS-MIB"),'"');

$hardware = $manufacturer . ' ' . $model;

?>