<?php

$index = $mempool['mempool_index'];

/// FIXME. Need using HC counters (cempMemPoolHCUsed, cempMemPoolHCFree, cempMemPoolHCLargestFree)
foreach (array('cempMemPoolUsed', 'cempMemPoolFree', 'cempMemPoolLargestFree') as $oid)
{
  $pool_data = snmpwalk_cache_multi_oid($device, $oid, $pool_data, 'CISCO-ENHANCED-MEMPOOL-MIB', mib_dirs('cisco'));
}

$mempool['used'] = $pool_data[$index]['cempMemPoolUsed'];
$mempool['free'] = $pool_data[$index]['cempMemPoolFree'];
$mempool['largestfree'] = $pool_data[$index]['cempMemPoolLargestFree'];

$mempool['total'] = $mempool['used'] + $mempool['free'];

?>
