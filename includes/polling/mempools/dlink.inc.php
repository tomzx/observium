<?php

## Fuckin D-Link!
# DES-3028P
#AGENT-GENERAL-MIB::agentDRAMutilizationTotalDRAM.1 = INTEGER: 65536 KB
#AGENT-GENERAL-MIB::agentDRAMutilizationUsedDRAM.1 = INTEGER: 41871 KB

# DES-35XX, DGS-34XX
#AGENT-GENERAL-MIB::agentDRAMutilizationTotalDRAM.1 = INTEGER: 22755168 KB
#AGENT-GENERAL-MIB::agentDRAMutilizationUsedDRAM.1 = INTEGER: 13516676 KB

$mempool['total'] = snmp_get($device, "agentDRAMutilizationTotalDRAM." . $mempool['mempool_index'], "-OUvq", "AGENT-GENERAL-MIB");
$mempool['used'] = snmp_get($device, "agentDRAMutilizationUsedDRAM." . $mempool['mempool_index'], "-OUvq", "AGENT-GENERAL-MIB");
if (strlen($mempool['total']) < 7)
{
  $mempool['total'] *= 1024;
  $mempool['used'] *= 1024;
}
$mempool['free'] = $mempool['total'] - $mempool['used'];
//$mempool['perc'] = snmp_get($device, "agentDRAMutilization." . $mempool['mempool_index'], "-Ovq", "DLINK-AGENT-MIB");

?>
