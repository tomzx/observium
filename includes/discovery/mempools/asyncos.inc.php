<?php

if ($device['os'] == "asyncos")
{
  echo("AsyncOS : ");

  $percent = snmp_get($device, "ASYNCOS-MAIL-MIB::perCentMemoryUtilization.0", "-OvQ");

  if (is_numeric($percent))
  {
    discover_mempool($valid_mempool, $device, 0, "asyncos", "Memory Utilization", "1", NULL, NULL);
  }

}

//EOF
