<?php

$oid = $mempool['mempool_index'];

$pool_data = snmp_get_multi($device, "cempMemPoolUsed.$oid cempMemPoolFree.$oid cempMemPoolLargestFree.$oid", '-OQUs', 'CISCO-ENHANCED-MEMPOOL-MIB', mib_dirs('cisco'));
$mempool['used'] = $pool_data[$oid]['cempMemPoolUsed'];
$mempool['free'] = $pool_data[$oid]['cempMemPoolFree'];
$mempool['largestfree'] = $pool_data[$oid]['cempMemPoolLargestFree'];

$mempool['total'] = $mempool['used'] + $mempool['free'];

?>
