<?php

$index = $mempool['mempool_index'];

foreach (array('ciscoMemoryPoolUsed', 'ciscoMemoryPoolFree', 'ciscoMemoryPoolLargestFree') as $oid)
{
  $pool_data = snmpwalk_cache_multi_oid($device, $oid, $pool_data, 'CISCO-MEMORY-POOL-MIB', mib_dirs('cisco'));
}
$mempool['used'] = $pool_data[$index]['ciscoMemoryPoolUsed'];
$mempool['free'] = $pool_data[$index]['ciscoMemoryPoolFree'];
$mempool['largestfree'] = $pool_data[$index]['ciscoMemoryPoolLargestFree'];

$mempool['total'] = $mempool['used'] + $mempool['free'];

?>
