<?php

  # lookup for memory data
  $data = snmp_walk($device, "extremeMemoryMonitorSystemTable", "-Oqs", "EXTREME-BASE-MIB", mib_dirs('extreme'));

  if (strstr($data, 'extremeMemoryMonitorSystemTotal'))
  {
    discover_mempool($valid_mempool, $device, 0, "xos", "Memory", "1024", NULL, NULL);

    if ($debug)
    {
      foreach (explode("\n", $data) as $entry)
      {
        $t = explode(" ", $entry, 2);

        if (strstr($t[0], 'extremeMemoryMonitorSystemTotal'))
        {
          echo sprintf("Memory: Total %d (KB)", $t[1]);
        }
      }
    }
  }

?>
