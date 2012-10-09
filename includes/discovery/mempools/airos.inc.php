<?php

# FROGFOOT-RESOURCES-MIB::memTotal.0 = Gauge32: 29524
# FROGFOOT-RESOURCES-MIB::memFree.0 = Gauge32: 4584
# FROGFOOT-RESOURCES-MIB::memBuffer.0 = Gauge32: 3584

if ($device['os'] == "airos")
{
  echo("Ubiquiti AirOS: ");
  $free = snmp_get($device, ".1.3.6.1.4.1.10002.1.1.1.1.2.0", "-Ovq", FROGFOOT-RESOURCES-MIB);
  $total = snmp_get($device, ".1.3.6.1.4.1.10002.1.1.1.1.1.0", "-Ovq", FROGFOOT-RESOURCES-MIB);
  $used = $total - $free;
  $percent = $used / $total * 100;
/*
echo $free . "\n";
echo $total . "\n";
echo $used . "\n";
echo $percent . "\n";
*/
  if (is_numeric($total) && is_numeric($used))
  {
    discover_mempool($valid_mempool, $device, 0, "airos", "Memory", "1", NULL, NULL);
  }
}
?>
