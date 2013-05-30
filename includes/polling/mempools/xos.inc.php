<?php

  # lookup for memory data
  $data = snmp_walk($device, "extremeMemoryMonitorSystemTable", "-Oqs", "EXTREME-BASE-MIB", mib_dirs('extreme'));

  $memory = array();
  foreach (explode("\n", $data) as $entry)
  {
    $t = explode(" ", $entry, 2);

    if (strstr($t[0], 'extremeMemoryMonitorSystemTotal')) {$total = $t[1] *1024;}
    if (strstr($t[0], 'extremeMemoryMonitorSystemFree')) {$avail = $t[1] *1024;}
    if (strstr($t[0], 'extremeMemoryMonitorSystemUsage')) {$usage = $t[1] *1024;}
    if (strstr($t[0], 'extremeMemoryMonitorUserUsage')) {$user = $t[1] *1024;}
  }

  echo sprintf("Total %d (B), Avail: %d (B), Used %d (B) ", $total, $avail, $usage + $user);

  $mempool['total'] = $total;
  $mempool['free'] = $avail;
  $mempool['used'] = $usage + $user;

?>
