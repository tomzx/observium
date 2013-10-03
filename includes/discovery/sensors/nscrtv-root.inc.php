<?php

/* Detection for JDSU OEM Erbium Dotted Fibre Amplifiers */

if ($device['os'] == "jdsu_edfa")
{
  // Voltage Sensors
  $oids = snmp_walk($device, "oaDCPowerName", "-OsqnU", "NSCRTV-ROOT");
  if ($debug) { echo($oids."\n"); }
  $divisor = 10;

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = ".1.3.6.1.4.1.17409.1.11.7.1.2." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "NSCRTV-ROOT") / $divisor;
      if ($descr == '+5v') {
        $low_limit = 4.8;
        $low_warn_limit = 4.9;
        $high_warn_limit = 5.2;
        $high_limit = 5.3;
      } elseif ($descr == '-5v') {
        $low_limit = -5.3;
        $low_warn_limit = -5.2;
        $high_warn_limit = -4.9;
        $high_limit = -4.8;  
      }

      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'jdsu-edfa-power', $descr, $divisor, 1, $low_limit, $low_warn_limit, $high_warn_limit, $high_limit, $current);
    }
  }
  
  // Pump Sensors
  $oids = snmp_walk($device, "oaPumpBIAS", "-OsqnU", "NSCRTV-ROOT");
  if ($debug) { echo($oids."\n"); }
  $divisor = 1000;

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = ".1.3.6.1.4.1.17409.1.11.4.1.2." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "NSCRTV-ROOT") / $divisor;
      
      if ($current != 0) {
        $descr = "Pump Bias $index";
        discover_sensor($valid['sensor'], 'current', $device, $oid, $index, 'jdsu-edfa-pump-bias', $descr, $divisor, 1, NULL, NULL, NULL, NULL, $current);
      }
    }
  }

  
  $oids = snmp_walk($device, "oaPumpTEC", "-OsqnU", "NSCRTV-ROOT");
  if ($debug) { echo($oids."\n"); }
  $divisor = 100;

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = ".1.3.6.1.4.1.17409.1.11.4.1.3." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "NSCRTV-ROOT") / $divisor;

      if ($current != 0) {
        $descr = "Pump TEC $index";
        discover_sensor($valid['sensor'], 'current', $device, $oid, $index, 'jdsu-edfa-pump-tec', $descr, $divisor, 1, NULL, NULL, NULL, NULL, $current);
      }
    }
  }

  $oids = snmp_walk($device, "oaPumpTemp", "-OsqnU", "NSCRTV-ROOT");
  if ($debug) { echo($oids."\n"); }
  $divisor = 10;

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = ".1.3.6.1.4.1.17409.1.11.4.1.4." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "NSCRTV-ROOT") / $divisor;

      if ($current != 0) {
        $descr = "Pump Temperature $index";
        discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, 'jdsu-edfa-pump-temp', $descr, $divisor, 1, NULL, NULL, NULL, NULL, $current);
      }
    }
  }
}

// EOF
