<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    obserium
 * @subpackage poller
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

// Simple hard-coded poller for Ciena

/// Good candidate to convert to an array-based discovery system.

echo "Ciena Mem: ";

$mempool['used'] = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.12.1.9.1.1.4.2", "-OvQU");
$mempool['total'] = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.12.1.9.1.1.2.2", "-OvQU");
$mempool['free'] = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.12.1.9.1.1.7.2", "-OvQU");

echo "(U: ".$mempool['used']." F: ".$mempool['free']." T: ".$mempool['total'].") ";

// EOF
