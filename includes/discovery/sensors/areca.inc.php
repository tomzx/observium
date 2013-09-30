<?php

# If only there was a valid (syntactically correct) MIB (and not one per controller sharing OIDs!)...
# This file would have been a lot cleaner, walking a complete sensor table, and picking values...

if ($device['os'] == "areca")
{
  $type = 'areca';

  $oids = snmp_walk($device, "1.3.6.1.4.1.18928.1.2.2.1.9.1.2", "-OsqnU", "");
  if ($debug) { echo($oids."\n"); }
  if ($oids) echo("Areca ");
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = "1.3.6.1.4.1.18928.1.2.2.1.9.1.3." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "");

      discover_sensor($valid['sensor'], 'fanspeed', $device, $oid, $index, $type, trim($descr,'"'), 1, 1, NULL, NULL, NULL, NULL, $current);
    }
  }

  $oids = snmp_walk($device, "1.3.6.1.4.1.18928.1.1.2.14.1.2", "-Osqn", "");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  if ($oids) echo("Areca Harddisk ");
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $temperature_id = $split_oid[count($split_oid)-1];
      $temperature_oid  = "1.3.6.1.4.1.18928.1.1.2.14.1.2.$temperature_id";
      $temperature  = snmp_get($device, $temperature_oid, "-Oqv", "");
      $descr = "Hard disk $temperature_id";
      if ($temperature != -128) # -128 = not measured/present
      {
        discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, zeropad($temperature_id), $type, $descr, 1, 1, NULL, NULL, NULL, NULL, $temperature);
      }
    }
  }

  $oids = snmp_walk($device, "1.3.6.1.4.1.18928.1.2.2.1.10.1.2", "-OsqnU", "");
  if ($debug) { echo($oids."\n"); }
  if ($oids) echo("Areca Controller ");
  $precision = 1;
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = "1.3.6.1.4.1.18928.1.2.2.1.10.1.3." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "");

      discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, $type, trim($descr,'"'), 1, 1, NULL, NULL, NULL, NULL, $current);
    }
  }

  $oids = snmp_walk($device, "1.3.6.1.4.1.18928.1.2.2.1.8.1.2", "-OsqnU", "");
  if ($debug) { echo($oids."\n"); }
  if ($oids) echo("Areca ");
  $divisor = 1000;
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = "1.3.6.1.4.1.18928.1.2.2.1.8.1.3." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "") / $divisor;
      if (trim($descr,'"') != 'Battery Status') # Battery Status is charge percentage, or 255 when no BBU
      {
        discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, $type, trim($descr,'"'), $divisor, 1, NULL, NULL, NULL, NULL, $current);
      }
    }
  }
}

// EOF