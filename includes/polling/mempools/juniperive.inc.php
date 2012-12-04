<?php

// just a wild guess that it's measured in %

$mempool['total'] = 100;
$mempool['used'] = snmp_get($device, "iveMemoryUtil.0", "-OvQ", "JUNIPER-IVE-MIB");
$mempool['free'] = $mempool['total'] - $mempool['used'];

$storage['total'] = 100;
$storage['used'] = snmp_get($device, "diskFullPercent.0", "-OvQ", "JUNIPER-IVE-MIB");
$storage['free'] = $storage['free'] - $storage['used']
?>
