<?php

# FROGFOOT-RESOURCES-MIB::memTotal.0 = Gauge32: 29524
# FROGFOOT-RESOURCES-MIB::memFree.0 = Gauge32: 4584

$mempool['total'] = snmp_get($device, "memTotal.0", "-OvQ", "FROGFOOT-RESOURCES-MIB");
$mempool['free'] = snmp_get($device, "memFree.0", "-OvQ", "FROGFOOT-RESOURCES-MIB");
$mempool['used'] = $mempool['total'] - $mempool['free'];

?>
