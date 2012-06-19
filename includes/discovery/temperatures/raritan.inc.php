<?php

if ($device['os'] == 'raritan')
{
  $divisor = 10;
  $outlet_divisor = $divisor;
  $multiplier = "1";

  $outlet_insert_index = 0;

  $outlet_oid   = ".1.3.6.1.4.1.13742.4.1.3.1.5.$outlet_insert_index";
  $outlet_descr   = "CPU Temperature";
  $outlet_low_warn_limit  = snmp_get($device,"unitTempLowerWarning.$outlet_insert_index", "-Ovq", "PDU-MIB");
  $outlet_low_limit     = snmp_get($device,"unitTempLowerCritical.$outlet_insert_index", "-Ovq", "PDU-MIB");
  $outlet_high_warn_limit = snmp_get($device,"unitTempUpperWarning.$outlet_insert_index", "-Ovq", "PDU-MIB");
  $outlet_high_limit    = snmp_get($device,"unitTempUpperCritical.$outlet_insert_index", "-Ovq", "PDU-MIB");
  $outlet_current = snmp_get($device,"unitCpuTemp.$outlet_insert_index", "-Ovq", "PDU-MIB") / $divisor; # Yeah, current divisor is different from limits...

  if ($outlet_current >= 0) {
    discover_sensor($valid['sensor'], 'temperature', $device, $outlet_oid, $outlet_insert_index, 'raritan', $outlet_descr, $outlet_divisor, $multiplier, $outlet_low_limit, $outlet_low_warn_limit, $outlet_high_warn_limit, $outlet_high_limit, $outlet_current);
  }

}

?>
