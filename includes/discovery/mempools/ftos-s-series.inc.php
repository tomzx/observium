<?php

// Force10 S-Series

#F10-S-SERIES-CHASSIS-MIB::chStackUnitMemUsageUtil.1 = Gauge32: 86

if ($device['os'] == "ftos" || $device['os_group'] == "ftos")
{
  echo("FTOS S-Series ");

  $mempools_array = snmpwalk_cache_oid($device, "chStackUnitMemUsageUtil", array(), "F10-S-SERIES-CHASSIS-MIB", mib_dirs('force10'));
  $mempools_array = snmpwalk_cache_oid($device, "chStackUnitSysType", $mempools_array, "F10-S-SERIES-CHASSIS-MIB", mib_dirs('force10'));
  if ($debug) { print_vars($mempools_array); }

  if (is_array($mempools_array))
  {
    foreach ($mempools_array as $index => $entry)
    {
      $descr = "Unit " . strval($index - 1) . " " . $entry['chStackUnitSysType'];
      discover_mempool($valid_mempool, $device, $index, "ftos-sseries", $descr, NULL, NULL, NULL);
    }
  }
}

unset ($mempools_array);

?>
