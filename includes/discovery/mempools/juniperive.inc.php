<?php

if ($device['os'] == "juniperive")
{
  echo("Juniper IVE: ");

  $percent = snmp_get($device, "iveMemoryUtil.0", "-OvQ", "JUNIPER-IVE-MIB");

  if (is_numeric($percent))
  {
    discover_mempool($valid_mempool, $device, 0, "juniperive", "Memory Utilization", "1", NULL, NULL);
  }

}
?>
