<?php
///
////  Hardcoded discovery of memory usage on SmartEdge devices.
/////
////  RBN-MEMORY-MIB::rbnMemoryKBytesInUse.0
////  RBN-MEMORY-MIB::rbnMemoryFreeKBytes.0
//

if ($device['os'] == "seos")
{
  echo("SmartEdge OS: ");

  $used = snmp_get($device, ".1.3.6.1.4.1.2352.2.16.1.2.1.4.1", "-OvQ");
  $free = snmp_get($device, ".1.3.6.1.4.1.2352.2.16.1.2.1.3.1", "-OvQ");

  if (is_numeric($free) && is_numeric($used))
  {
    discover_mempool($valid_mempool, $device, 0, "seos", "Memory", "1", NULL, NULL);
  }
}

/* End of file seos.inc.php */
