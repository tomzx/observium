<?php

if ($device['os'] == "aos")
{
  echo("Alcatel-Lucent OS: ");

  $total   = snmp_get($device, "systemHardwareMemorySize.0", "-OvQ", "ALCATEL-IND1-SYSTEM-MIB", mib_dirs('aos'));
  $percent = snmp_get($device, "healthDeviceMemoryLatest.0", "-OvQ", "ALCATEL-IND1-HEALTH-MIB", mib_dirs('aos'));
  $used = $total / 100 * $perc_used;
  $free = $total - $used;

  if (is_numeric($total) && is_numeric($used))
  {
    discover_mempool($valid_mempool, $device, 0, "aos-device", "Device Memory", "1", NULL, NULL);
  }
}
?>
