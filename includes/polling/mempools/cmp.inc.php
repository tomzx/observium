<?php

$oid = $mempool['mempool_index'];

$pool_data = snmp_get_multi($device, "ciscoMemoryPoolUsed.$oid ciscoMemoryPoolFree.$oid ciscoMemoryPoolLargestFree.$oid", '-OQUs', 'CISCO-MEMORY-POOL-MIB', mib_dirs('cisco'));
$mempool['used'] = $pool_data[$oid]['ciscoMemoryPoolUsed'];
$mempool['free'] = $pool_data[$oid]['ciscoMemoryPoolFree'];
$mempool['largestfree'] = $pool_data[$oid]['ciscoMemoryPoolLargestFree'];

$mempool['total'] = $mempool['used'] + $mempool['free'];

?>
