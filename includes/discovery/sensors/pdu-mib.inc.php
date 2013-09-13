<?php

// FIXME - maybe this one should also be using table walks instead of $outlet_index and $outletsuffix hacks. (but I don't have a device) -TL

if ($device['os'] == 'raritan')
{

  /////////////////////////////////
  # Check for per-outlet polling
  $outlet_oids = snmp_walk($device, "outletIndex", "-Osqn", "PDU-MIB");
  $outlet_oids = trim($outlet_oids);

  if ($outlet_oids) echo("PDU Outlet ");

  $ratedvoltage = snmp_get($device,"ratedVoltage.0", "-Ovq", "PDU-MIB");

  foreach (explode("\n", $outlet_oids) as $outlet_data)
  {
    $outlet_data = trim($outlet_data);
    if ($outlet_data)
    {
      list($outlet_oid,$outlet_descr) = explode(" ", $outlet_data,2);
      $outlet_split_oid = explode('.',$outlet_oid);
      $outlet_index = $outlet_split_oid[count($outlet_split_oid)-1];

      $outletsuffix = "$outlet_index";
      $outlet_insert_index = $outlet_index;

      #outletLoadValue: "A non-negative value indicates the measured load in milli Amps"
      $outlet_oid             = ".1.3.6.1.4.1.13742.4.1.2.2.1.4.$outletsuffix";
      $outlet_descr           = snmp_get($device,"outletLabel.$outletsuffix", "-Ovq", "PDU-MIB");
      $outlet_low_warn_limit  = snmp_get($device,"outletCurrentLowerWarning.$outletsuffix", "-Ovq", "PDU-MIB") / 1000;
      $outlet_low_limit       = snmp_get($device,"outletCurrentLowerCritical.$outletsuffix", "-Ovq", "PDU-MIB") / 1000;
      $outlet_high_warn_limit = snmp_get($device,"outletCurrentUpperWarning.$outletsuffix", "-Ovq", "PDU-MIB") / 1000;
      $outlet_high_limit      = snmp_get($device,"outletCurrentUpperCritical.$outletsuffix", "-Ovq", "PDU-MIB") / 1000;
      $outlet_current         = snmp_get($device,"outletCurrent.$outletsuffix", "-Ovq", "PDU-MIB") / 1000;

      if ($outlet_current >= 0)
      {
        discover_sensor($valid['sensor'], 'current', $device, $outlet_oid, $outlet_insert_index, 'raritan', $outlet_descr, 1000, 1, $outlet_low_limit, $outlet_low_warn_limit, $outlet_high_warn_limit, $outlet_high_limit, $outlet_current);
      }

      
      $outlet_oid       = ".1.3.6.1.4.1.13742.4.1.2.2.1.8.$outletsuffix";
      $outlet_descr     = snmp_get($device,"outletLabel.$outletsuffix", "-Ovq", "PDU-MIB");
      $outlet_low_warn_limit  = intval((snmp_get($device,"outletCurrentLowerWarning.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);
      $outlet_low_limit       = intval((snmp_get($device,"outletCurrentLowerCritical.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);
      $outlet_high_warn_limit = intval((snmp_get($device,"outletCurrentUpperWarning.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);
      $outlet_high_limit      = intval((snmp_get($device,"outletCurrentUpperCritical.$outletsuffix", "-Ovq", "PDU-MIB") / 1000) * $ratedvoltage);

      $outlet_current   = snmp_get($device,"outletApparentPower.$outletsuffix", "-Ovq", "PDU-MIB");

      if ($outlet_current >= 0)
      {
        discover_sensor($valid['sensor'], 'power', $device, $outlet_oid, $outlet_insert_index, 'raritan', $outlet_descr, 1, 1, $outlet_low_limit, $outlet_low_warn_limit, $outlet_high_warn_limit, $outlet_high_limit, $outlet_current);
      }
    } // if ($outlet_data)
  } // foreach (explode("\n", $outlet_oids) as $outlet_data)

  $outlet_oid             = ".1.3.6.1.4.1.13742.4.1.3.1.5.0";
  $outlet_descr           = "CPU Temperature";
  $outlet_low_warn_limit  = snmp_get($device,"unitTempLowerWarning.0", "-Ovq", "PDU-MIB");
  $outlet_low_limit       = snmp_get($device,"unitTempLowerCritical.0", "-Ovq", "PDU-MIB");
  $outlet_high_warn_limit = snmp_get($device,"unitTempUpperWarning.0", "-Ovq", "PDU-MIB");
  $outlet_high_limit      = snmp_get($device,"unitTempUpperCritical.0", "-Ovq", "PDU-MIB");
  $outlet_current         = snmp_get($device,"unitCpuTemp.0", "-Ovq", "PDU-MIB") / 10; # Yeah, current divisor is different from limits...

  if ($outlet_current >= 0)
  {
    discover_sensor($valid['sensor'], 'temperature', $device, $outlet_oid, 0, 'raritan', $outlet_descr, 10, 1, $outlet_low_limit, $outlet_low_warn_limit, $outlet_high_warn_limit, $outlet_high_limit, $outlet_current);
  }

  $outlet_oid             = ".1.3.6.1.4.1.13742.4.1.3.1.2.0";
  $outlet_descr           = "Input Feed";
  $outlet_low_warn_limit  = snmp_get($device,"unitOrLineVoltageLowerWarning.0", "-Ovq", "PDU-MIB") / 1000;
  $outlet_low_limit       = snmp_get($device,"unitOrLineVoltageLowerCritical.0", "-Ovq", "PDU-MIB") / 1000;
  $outlet_high_warn_limit = snmp_get($device,"unitOrLineVoltageUpperWarning.0", "-Ovq", "PDU-MIB") / 1000;
  $outlet_high_limit      = snmp_get($device,"unitOrLineVoltageUpperCritical.0", "-Ovq", "PDU-MIB") / 1000;
  $outlet_current         = snmp_get($device,"unitVoltage.0", "-Ovq", "PDU-MIB") / 1000;

  if ($outlet_current >= 0)
  {
    discover_sensor($valid['sensor'], 'voltage', $device, $outlet_oid, 0, 'raritan', $outlet_descr, 1000, 1, $outlet_low_limit, $outlet_low_warn_limit, $outlet_high_warn_limit, $outlet_high_limit, $outlet_current);
  }

}

// EOF
