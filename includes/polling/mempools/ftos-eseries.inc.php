<?php

echo "Force10 E-Series Mempool";

$index = $mempool['mempool_index'];

$mempool['total'] = snmp_get($device, "F10-CHASSIS-MIB::chSysProcessorMemSize.1." . $mempool['mempool_index'], "-OvQ");
$mempool['total'] *= 1048576; // FTOS display memory in MB
$mempool['perc'] = snmp_get($device, "F10-CHASSIS-MIB::chRpmMemUsageUtil." . $mempool['mempool_index'], "-OvQ");

$mempool['used'] = $mempool['total'] * $mempool['perc'] / 100;
$mempool['free'] = $mempool['total'] - $mempool['used'];

?>
