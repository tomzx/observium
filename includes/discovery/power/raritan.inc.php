<?php

if ($device['os'] == 'raritan')
{
  $divisor = "1";
  $outlet_divisor = $divisor;
  $multiplier = "1";
  
  $ratedvoltage = snmp_get($device,"ratedVoltage.0", "-Ovq", "PDU-MIB");

  /////////////////////////////////
  # Check for per-outlet polling
  $outlet_oids = snmp_walk($device, "outletIndex", "-Osqn", "PDU-MIB");
  $outlet_oids = trim($outlet_oids);

  if ($outlet_oids) echo("PDU Outlet ");
  foreach (explode("\n", $outlet_oids) as $outlet_data)
  {
    $outlet_data = trim($outlet_data);
    if ($outlet_data)
    {
      list($outlet_oid,$outlet_descr) = explode(" ", $outlet_data,2);
      $outlet_split_oid = explode('.',$outlet_oid);
      $outlet_index = $outlet_split_oid[count($outlet_split_oid)-1];

      $outletsuffix = "$outlet_index";
      $outlet_insert_index=$outlet_index;

      $outlet_oid       = ".1.3.6.1.4.1.13742.4.1.2.2.1.8.$outletsuffix";
      $outlet_descr     = snmp_get($device,"outletLabel.$outletsuffix", "-Ovq", "PDU-MIB");
      $outlet_low_warn_limit  = intval((snmp_get($device,"outletCurrentLowerWarning.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);
      $outlet_low_limit       = intval((snmp_get($device,"outletCurrentLowerCritical.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);
      $outlet_high_warn_limit = intval((snmp_get($device,"outletCurrentUpperWarning.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);
      $outlet_high_limit      = intval((snmp_get($device,"outletCurrentUpperCritical.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);

      $outlet_current   = snmp_get($device,"outletApparentPower.$outletsuffix", "-Ovq", "PDU-MIB") / $outlet_divisor;

      if ($outlet_current >= 0) {
        discover_sensor($valid['sensor'], 'power', $device, $outlet_oid, $outlet_insert_index, 'raritan', $outlet_descr, $outlet_divisor, $multiplier, $outlet_low_limit, $outlet_low_warn_limit, $outlet_high_warn_limit, $outlet_high_limit, $outlet_current);
      }
    }
  } 
}

?>
