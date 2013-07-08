<?php

$mempool['total'] = 100;
$mempool['used'] = snmp_get($device, "ASYNCOS-MAIL-MIB::perCentMemoryUtilization.0", "-OvQ");
$mempool['free'] = $mempool['total'] - $mempool['used'];

echo "(U: ".$mempool['used']." T: ".$mempool['total']." F: ".$mempool['free'].") ";

// EOF
