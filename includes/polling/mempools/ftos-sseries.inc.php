<?php

echo "Force10 S-Series Mempool";

$index = $mempool['mempool_index'];
$mempool['total'] = snmp_get($device, "F10-S-SERIES-CHASSIS-MIB::chSysProcessorMemSize." . $mempool['mempool_index'], "-OvQ");
$mempool['perc'] = snmp_get($device, "F10-S-SERIES-CHASSIS-MIB::chStackUnitMemUsageUtil." . $mempool['mempool_index'], "-OvQ");

$mempool['used'] = $mempool['total'] * $mempool['perc'] / 100;
$mempool['free'] = $mempool['total'] - $mempool['used'];

?>
