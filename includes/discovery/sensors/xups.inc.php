<?php

// XUPS-MIB
if ($device['os'] == "powerware")
{
  echo("XUPS-MIB ");

  $oids = snmp_walk($device, "xupsBatCurrent", "-Osqn", "XUPS-MIB");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $current_id = $split_oid[count($split_oid)-1];
      $current_oid  = "1.3.6.1.4.1.534.1.2.3.$current_id";
      $divisor = 1;
      $current = snmp_get($device, $current_oid, "-O vq");
      $descr = "Battery" . (count(explode("\n",$oids)) == 1 ? '' : ' ' . ($current_id+1));
      $type = "xups";
      $index = "1.2.3.".$current_id;

      discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  }

  $oids = trim(snmp_walk($device, "xupsOutputCurrent", "-OsqnU", "XUPS-MIB"));
  if ($debug) { echo($oids."\n"); }
  list($unused,$numPhase) = explode(' ',$oids);
  for($i = 1; $i <= $numPhase;$i++)
  {
    $current_oid  = "1.3.6.1.4.1.534.1.4.4.1.3.$i";
    $descr      = "Output"; if ($numPhase > 1) $descr .= " Phase $i";
    $current    = snmp_get($device, $current_oid, "-Oqv");
    $type       = "xups";
    $divisor    = 1;
    $index      = "4.4.1.3.".$i;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }

  $oids = trim(snmp_walk($device, "xupsInputCurrent", "-OsqnU", "XUPS-MIB"));
  if ($debug) { echo($oids."\n"); }
  list($unused,$numPhase) = explode(' ',$oids);
  for($i = 1; $i <= $numPhase;$i++)
  {
    $current_oid   = "1.3.6.1.4.1.534.1.3.4.1.3.$i";
    $descr      = "Input"; if ($numPhase > 1) $descr .= " Phase $i";
    $current    = snmp_get($device, $current_oid, "-Oqv");
    $type       = "xups";
    $divisor    = 1;
    $index      = "3.4.1.3.".$i;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }

  # I'm not sure if there is provision for frequency of multiple phases in this MIB -TL

  # XUPS-MIB::xupsInputFrequency.0 = INTEGER: 500
  $freq_oid = ".1.3.6.1.4.1.534.1.3.1.0";
  $descr    = "Input";
  $divisor  = 10;
  $current  = snmp_get($device, $freq_oid, "-Oqv") / $divisor;
  $type     = "xups";
  $index    = '3.1.0';
  discover_sensor($valid['sensor'], 'frequency', $device, $freq_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);

  # XUPS-MIB::xupsOutputFrequency.0 = INTEGER: 500
  $freq_oid = "1.3.6.1.4.1.534.1.4.2.0";
  $descr    = "Output";
  $divisor  = 10;
  $current  = snmp_get($device, $freq_oid, "-Oqv") / $divisor;
  $type     = "xups";
  $index    = '4.2.0';
  discover_sensor($valid['sensor'], 'frequency', $device, $freq_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);

  # XUPS-MIB::xupsBypassFrequency.0 = INTEGER: 500
  $freq_oid = "1.3.6.1.4.1.534.1.5.1.0";
  $descr    = "Bypass";
  $divisor  = 10;
  $current  = snmp_get($device, $freq_oid, "-Oqv");
  if ($current != "")
  {
    # Bypass is not always available in SNMP
    $current /= $divisor;
    $type     = "xups";
    $index    = '5.1.0';
    discover_sensor($valid['sensor'], 'frequency', $device, $freq_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }

  # XUPS-MIB::xupsEnvAmbientTemp.0 = INTEGER: 52
  # XUPS-MIB::xupsEnvAmbientLowerLimit.0 = INTEGER: 0
  # XUPS-MIB::xupsEnvAmbientUpperLimit.0 = INTEGER: 70
  $oids = snmp_walk($device, "xupsEnvAmbientTemp", "-Osqn", "XUPS-MIB");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  if ($oids) echo("Powerware Ambient Temperature ");
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $temperature_id = $split_oid[count($split_oid)-1];
      $temperature_oid  = ".1.3.6.1.4.1.534.1.6.1.$temperature_id";
      $lowlimit = snmp_get($device,"upsEnvAmbientLowerLimit.$temperature_id", "-Ovq", "XUPS-MIB");
      $highlimit = snmp_get($device,"upsEnvAmbientUpperLimit.$temperature_id", "-Ovq", "XUPS-MIB");
      $temperature = snmp_get($device, $temperature_oid, "-Ovq");
      $descr = "Ambient" . (count(explode("\n",$oids)) == 1 ? '' : ' ' . ($temperature_id+1));

      discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, '1.6.1.'.$temperature_id, 'powerware', $descr, '1', '1', $lowlimit, NULL, NULL, $highlimit, $temperature);
    }
  }

  # XUPS-MIB::xupsBatVoltage.0 = INTEGER: 51
  $oids = snmp_walk($device, "xupsBatVoltage", "-Osqn", "XUPS-MIB");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $volt_id = $split_oid[count($split_oid)-1];
      $volt_oid  = ".1.3.6.1.4.1.534.1.2.2.$volt_id";
      $divisor = 1;
      $volt = snmp_get($device, $volt_oid, "-O vq") / $divisor;
      $descr = "Battery" . (count(explode("\n",$oids)) == 1 ? '' : ' ' . ($volt_id+1));
      $type = "xups";
      $index = '1.2.5.'.$volt_id;

      discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $volt);
    }
  }

  # XUPS-MIB::xupsInputNumPhases.0 = INTEGER: 1
  $oids = trim(snmp_walk($device, "xupsInputNumPhases", "-OsqnU", "XUPS-MIB"));
  if ($debug) { echo($oids."\n"); }
  list($unused,$numPhase) = explode(' ',$oids);
  for($i = 1; $i <= $numPhase;$i++)
  {
    # XUPS-MIB::xupsInputVoltage.1 = INTEGER: 228
    $volt_oid = ".1.3.6.1.4.1.534.1.3.4.1.2.$i";
    $descr    = "Output"; if ($numPhase > 1) $descr .= " Phase $i";
    $type     = "xups";
    $divisor  = 1;
    $current  = snmp_get($device, $volt_oid, "-Oqv") / $divisor;
    $index    = '3.4.1.2.'.$i;

    discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }

  # XUPS-MIB::xupsOutputNumPhases.0 = INTEGER: 1
  $oids = trim(snmp_walk($device, "xupsOutputNumPhases", "-OsqnU"));
  if ($debug) { echo($oids."\n"); }
  list($unused,$numPhase) = explode(' ',$oids);
  for($i = 1; $i <= $numPhase;$i++)
  {
    # XUPS-MIB::xupsOutputVoltage.1 = INTEGER: 228
    $volt_oid = ".1.3.6.1.4.1.534.1.4.4.1.2.$i";
    $descr    = "Output"; if ($numPhase > 1) $descr .= " Phase $i";
    $type     = "xups";
    $divisor  = 1;
    $current  = snmp_get($device, $volt_oid, "-Oqv") / $divisor;
    $index    = '4.4.1.2.'.$i;

    discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }

  # XUPS-MIB::xupsBypassNumPhases.0 = INTEGER: 1
  $oids = trim(snmp_walk($device, "xupsBypassNumPhases", "-OsqnU"));
  if ($debug) { echo($oids."\n"); }
  list($unused,$numPhase) = explode(' ',$oids);
  for($i = 1; $i <= $numPhase;$i++)
  {
    $volt_oid = ".1.3.6.1.4.1.534.1.5.3.1.2.$i";
    $descr    = "Bypass"; if ($numPhase > 1) $descr .= " Phase $i";
    $type     = "xups";
    $divisor  = 1;
    $current  = snmp_get($device, $volt_oid, "-Oqv") / $divisor;
    $index    = '5.3.1.2.'.$i;

    discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }
}

?>