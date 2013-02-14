<?php

# DGS-3450
#AGENT-GENERAL-MIB::agentDRAMutilizationUnitID.0 = INTEGER: 0
#AGENT-GENERAL-MIB::agentDRAMutilizationTotalDRAM.0 = INTEGER: 262144 KB
#AGENT-GENERAL-MIB::agentDRAMutilizationUsedDRAM.0 = INTEGER: 174899 KB
#AGENT-GENERAL-MIB::agentDRAMutilization.0 = INTEGER: 66

# DES-3550, DES-3526, DES-3028 (and other Stacking switches)
# AGENT-GENERAL-MIB::agentDRAMutilizationUnitID.1 = INTEGER: 1
# AGENT-GENERAL-MIB::agentDRAMutilizationTotalDRAM.1 = INTEGER: 22495072 KB
# AGENT-GENERAL-MIB::agentDRAMutilizationUsedDRAM.1 = INTEGER: 12431462 KB
# AGENT-GENERAL-MIB::agentDRAMutilization.1 = INTEGER: 55

if ($device['os'] == "dlink")
{
  echo("D-Link MemPools: ");

  $mempools_array = snmpwalk_cache_oid($device, "agentDRAMutilizationUnitID", array(), "AGENT-GENERAL-MIB");
  if ($debug) { print_r($mempools_array); }

  if (is_array($mempools_array))
  {
    foreach ($mempools_array as $index => $entry)
    {
      $descr = ($index === 0) ? "Memory" : "Unit " . $index;
      discover_mempool($valid_mempool, $device, $index, "dlink", $descr, NULL, NULL, NULL);
    }
  }

}

unset($mempools_array);

?>
