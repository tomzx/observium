<?php

// APC
if ($device['os'] == "apc")
{
  $oids = snmp_walk($device, "1.3.6.1.4.1.318.1.1.8.5.3.2.1.4", "-OsqnU", "");
  if ($debug) { echo($oids."\n"); }
  if ($oids) echo("APC In ");
  $divisor = 1;
  $type = "apc";
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$current) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = "1.3.6.1.4.1.318.1.1.8.5.3.2.1.4." . $index;
      $descr = "Input Feed " . chr(64+$index);
      discover_sensor($valid['sensor'], 'frequency', $device, $oid, "3.2.1.4.$index", $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  }

  $oids = snmp_walk($device, "1.3.6.1.4.1.318.1.1.8.5.4.2.1.4", "-OsqnU", "");
  if ($debug) { echo($oids."\n"); }
  if ($oids) echo(" APC Out ");
  $divisor = 1;
  $type = "apc";
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$current) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-3];
      $oid  = "1.3.6.1.4.1.318.1.1.8.5.4.2.1.4." . $index;
      $descr = "Output Feed"; if (count(explode("\n", $oids)) > 1) { $descr .= " $index"; }
      discover_sensor($valid['sensor'], 'frequency', $device, $oid, "4.2.1.4.$index", $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  }

  # Fetch high precision frequency (Precision 0.1)
  $oids = snmp_get($device, "upsHighPrecInputFrequency.0", "-OsqnU", "PowerNet-MIB");
  if ($debug) { echo($oids."\n"); }
  if ($oids)
  {
    echo(" APC In ");
    list($oid,$current) = explode(" ",$oids);
    $divisor = 10;
    $current /= $divisor;
    $type = "apc";
    $index = "3.3.4.0";
    $descr = "Input";
    discover_sensor($valid['sensor'], 'frequency', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }
  else
  {
    # If this is not available, fetch regular frequency (Precision 1)
    $oids = snmp_get($device, "upsAdvInputFrequency.0", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids)
    {
      echo(" APC In ");
      list($oid,$current) = explode(" ",$oids);
      $divisor = 1;
      $type = "apc";
      $index = "3.2.4.0";
      $descr = "Input";
      discover_sensor($valid['sensor'], 'frequency', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  }

  # Fetch high precision frequency (Precision 0.1)
  $oids = snmp_get($device, "upsHighPrecOutputFrequency.0", "-OsqnU", "PowerNet-MIB");
  if ($debug) { echo($oids."\n"); }
  if ($oids)
  {
    echo(" APC Out ");
    list($oid,$current) = explode(" ",$oids);
    $divisor = 10;
    $current /= $divisor;
    $type = "apc";
    $index = "4.3.2.0";
    $descr = "Input";
    discover_sensor($valid['sensor'], 'frequency', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
  }
  else
  {
    # If this is not available, fetch regular frequency (Precision 1)
    $oids = snmp_get($device, "upsAdvOutputFrequency.0", "-OsqnU", "");
    if ($debug) { echo($oids."\n"); }
    if ($oids)
    {
      echo(" APC Out ");
      list($oid,$current) = explode(" ",$oids);
      $divisor = 1;
      $type = "apc";
      $index = "4.2.2.0";
      $descr = "Output";
      discover_sensor($valid['sensor'], 'frequency', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  }
}

?>