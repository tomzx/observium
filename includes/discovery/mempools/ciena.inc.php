<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    obserium
 * @subpackage discovery
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */


if ($device['os'] == "ciena")
{
  echo("Ciena Mem: ");

  $used = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.12.1.9.1.1.4.2", "-OvqU");
  $total = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.12.1.9.1.1.2.2", "-OvQU");
  $free = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.12.1.9.1.1.7.2", "-OvQU");

  if (is_numeric($total) && is_numeric($used))
  {
    discover_mempool($valid_mempool, $device, 0, "ciena", "Memory", "1", NULL, NULL);
  }
}

// EOF
