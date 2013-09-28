<?php

// Simple hard-coded poller for Juniper ScreenOS
// Yes, it really can be this simple.

// FIXME - this should really just be a definition array somewhere...

$mempool['used'] = snmp_get($device, ".1.3.6.1.4.1.3224.16.2.1.0", "-OvQ");
$mempool['free'] = snmp_get($device, ".1.3.6.1.4.1.3224.16.2.2.0", "-OvQ");
$mempool['total'] = $mempool['used'] + $mempool['free'];

?>
