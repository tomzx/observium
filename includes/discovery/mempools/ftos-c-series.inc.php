<?php

// Force10 C-Series

#F10-C-SERIES-CHASSIS-MIB::chRpmMemUsageUtil.1 = 5

if ($device['os'] == "ftos" || $device['os_group'] == "ftos")
{
  echo("FTOS C-Series MemPools");

  $mempools_array = snmpwalk_cache_oid($device, "chRpmMemUsageUtil", array(), "F10-C-SERIES-CHASSIS-MIB", mib_dirs('force10'));
  if ($debug) { print_vars($mempools_array); }

  if (is_array($mempools_array))
  {
    foreach ($mempools_array as $index => $entry)
    {
      $descr = ($index == 1) ? "CP" : "RP" . strval($index - 1);
      discover_mempool($valid_mempool, $device, $index, "ftos-cseries", $descr, NULL, NULL, NULL);
    }
  }
}

unset ($mempools_array);
?>
