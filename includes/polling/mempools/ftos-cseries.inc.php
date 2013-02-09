<?php

echo "Force10 C-Series Mempool";

$index = $mempool['mempool_index'];

// FIXME. Here hardcoded memory size, because in not tested on real device.
// But this is not important, because Force10 immediately give memory usage as a percentage.
//$mempool['total'] = snmp_get($device, "F10-C-SERIES-CHASSIS-MIB::chSysProcessorMemSize." . $mempool['mempool_index'], "-OvQ");
$mempool['total'] = 1024;
$mempool['perc'] = snmp_get($device, "F10-C-SERIES-CHASSIS-MIB::chRpmMemUsageUtil." . $mempool['mempool_index'], "-OvQ");

$mempool['used'] = $mempool['total'] * $mempool['perc'] / 100;
$mempool['free'] = $mempool['total'] - $mempool['used'];

?>
