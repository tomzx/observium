<?php

// Force10 E-Series

#F10-CHASSIS-MIB::chRpmCpuUtil5Min.1 = Gauge32: 34
#F10-CHASSIS-MIB::chRpmCpuUtil5Min.2 = Gauge32: 34
#F10-CHASSIS-MIB::chRpmCpuUtil5Min.3 = Gauge32: 34

if ($device['os'] == "ftos" || $device['os_group'] == "ftos")
{
  echo("FTOS E-Series ");

  $processors_array = snmpwalk_cache_oid($device, "chRpmCpuUtil5Min", array(), "F10-CHASSIS-MIB", mib_dirs('force10'));
  if ($debug) { print_vars($processors_array); }

  if (is_array($processors_array))
  {
    foreach ($processors_array as $index => $entry)
    {
      $descr = ($index == 1) ? "CP" : "RP" . strval($index - 1);
      $oid = ".1.3.6.1.4.1.6027.3.1.1.3.7.1.5.".$index;
      $usage = $entry['chRpmCpuUtil5Min'];

      discover_processor($valid['processor'], $device, $oid, $index, "ftos-eseries", $descr, "1", $usage, NULL, NULL);
    }
  }
}

unset ($processors_array);

?>
