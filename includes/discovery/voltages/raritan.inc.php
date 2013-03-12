<?php

// blindly copied from sentry3

if ($device['os'] == 'raritan')
{
  $divisor = 1000;
  $outlet_divisor = $divisor;
  $multiplier = "1";

  $outlet_insert_index = 0;

  $outlet_oid   = ".1.3.6.1.4.1.13742.4.1.3.1.2.$outlet_insert_index";
  $outlet_descr   = "Input Feed";
  $outlet_low_warn_limit  = snmp_get($device,"unitOrLineVoltageLowerWarning.$outlet_insert_index", "-Ovq", "PDU-MIB") / $divisor;
  $outlet_low_limit     = snmp_get($device,"unitOrLineVoltageLowerCritical.$outlet_insert_index", "-Ovq", "PDU-MIB") / $divisor;
  $outlet_high_warn_limit = snmp_get($device,"unitOrLineVoltageUpperWarning.$outlet_insert_index", "-Ovq", "PDU-MIB") / $divisor;
  $outlet_high_limit    = snmp_get($device,"unitOrLineVoltageUpperCritical.$outlet_insert_index", "-Ovq", "PDU-MIB") / $divisor;
  $outlet_current = snmp_get($device,"unitVoltage.$outlet_insert_index", "-Ovq", "PDU-MIB") / $divisor;

  if ($outlet_current >= 0) {
    discover_sensor($valid['sensor'], 'voltage', $device, $outlet_oid, $outlet_insert_index, 'raritan', $outlet_descr, $outlet_divisor, $multiplier, $outlet_low_limit, $outlet_low_warn_limit, $outlet_high_warn_limit, $outlet_high_limit, $outlet_current);
  }

}

?>
