<?php

// Force10 E-Series

#F10-CHASSIS-MIB::chRpmMemUsageUtil.1 = 5
#F10-CHASSIS-MIB::chRpmMemUsageUtil.2 = 36
#F10-CHASSIS-MIB::chRpmMemUsageUtil.3 = 9

if ($device['os'] == "ftos" || $device['os_group'] == "ftos")
{
  echo("FTOS E-Series MemPools");

  $mempools_array = snmpwalk_cache_oid($device, "chRpmMemUsageUtil", array(), "F10-CHASSIS-MIB", mib_dirs('force10'));
  if ($debug) { print_vars($mempools_array); }

  if (is_array($mempools_array))
  {
    foreach ($mempools_array as $index => $entry)
    {
      $descr = ($index == 1) ? "CP" : "RP" . strval($index - 1);
      discover_mempool($valid_mempool, $device, $index, "ftos-eseries", $descr, NULL, NULL, NULL);
    }
  }
}

unset ($mempools_array);
?>
