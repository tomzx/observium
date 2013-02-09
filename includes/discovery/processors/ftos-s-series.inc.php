<?php

// Force10 S-Series

#F10-S-SERIES-CHASSIS-MIB::chStackUnitCpuUtil5Min.1 = Gauge32: 47

if ($device['os'] == "ftos" || $device['os_group'] == "ftos")
{
  echo("FTOS S-Series ");

  $processors_array = snmpwalk_cache_oid($device, "chStackUnitCpuUtil5Min", array(), "F10-S-SERIES-CHASSIS-MIB", $config['mib_dir'].":".$config['mib_dir']."/ftos" );
  $processors_array = snmpwalk_cache_oid($device, "chStackUnitSysType", $processors_array, "F10-S-SERIES-CHASSIS-MIB", $config['mib_dir'].":".$config['mib_dir']."/ftos" );
  if ($debug) { print_r($processors_array); }

  if (is_array($processors_array))
  {
    foreach ($processors_array as $index => $entry)
    {
      $descr = "Unit " . strval($index - 1) . " " . $entry['chStackUnitSysType'];
      $oid = ".1.3.6.1.4.1.6027.3.10.1.2.9.1.4.".$index;
      $usage = $entry['chStackUnitCpuUtil5Min'];

      discover_processor($valid['processor'], $device, $oid, $index, "ftos-sseries", $descr, "1", $usage, NULL, NULL);
    }
  }
}

unset ($processors_array);

?>
