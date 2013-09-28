<?php

# FIXME - consolidated 4 files, but could most certainly do with a rewrite; weird 3-phase-only non-table-walking code ahead! -TL

if ($device['os'] == "netvision")
{
  echo(" SICONUPS-MIB ");

  for ($i = 1; $i <= 3; $i++)
  {
    $current_oid = "1.3.6.1.4.1.4555.1.1.1.1.3.3.1.3.$i";
    $descr = "Input Phase $i";
    $current = snmp_get($device, $current_oid, "-Oqv");
    $precision = 1;
    $index = $i;
    $lowlimit = 0;
    $warnlimit = NULL;
    $limit = NULL;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, 'netvision', $descr, '10', '1', $lowlimit, NULL, NULL, NULL, $current);
  }

  for ($i = 1; $i <= 3; $i++)
  {
    $current_oid = "1.3.6.1.4.1.4555.1.1.1.1.4.4.1.3.$i";
    $descr = "Output Phase $i";
    $current = snmp_get($device, $current_oid, "-Oqv");
    $precision = 1;
    $index = 100+$i;
    $lowlimit = 0;
    $warnlimit = NULL;
    $limit = NULL;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, 'netvision', $descr, '10', '1', $lowlimit, NULL, NULL, NULL, $current);
  }

  $freq_oid   = "1.3.6.1.4.1.4555.1.1.1.1.3.2.0";
  $descr      = "Input";
  $current    = snmp_get($device, $freq_oid, "-Oqv") / 10;
  $divisor  = 10;
  $index      = '3.2.0';
  discover_sensor($valid['sensor'], 'frequency', $device, $freq_oid, $index, 'netvision', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);

  $freq_oid   = "1.3.6.1.4.1.4555.1.1.1.1.4.2.0";
  $descr      = "Output";
  $current    = snmp_get($device, $freq_oid, "-Oqv") / 10;
  $divisor  = 10;
  $index      = '4.2.0';
  discover_sensor($valid['sensor'], 'frequency', $device, $freq_oid, $index, 'netvision', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);

  // Battery voltage
  $volt_oid = "1.3.6.1.4.1.4555.1.1.1.1.2.5.0";
  $descr = "Battery";
  $volt = snmp_get($device, $volt_oid, "-Oqv");
  $divisor = 10;
  $index = 200;

  discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, 'netvision', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $volt);

  for ($i = 1; $i <= 3 ;$i++)
  {
    $volt_oid   = "1.3.6.1.4.1.4555.1.1.1.1.3.3.1.2.$i";
    $descr = "Input Phase $i";
    $volt = snmp_get($device, $volt_oid, "-Oqv");
    $divisor = 10;
    $index = $i;

    discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, 'netvision', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $volt);
  }

  for ($i = 1; $i <= 3 ;$i++)
  {
    $volt_oid   = "1.3.6.1.4.1.4555.1.1.1.1.4.4.1.2.$i";
    $descr = "Output Phase $i";
    $volt = snmp_get($device, $volt_oid, "-Oqv");
    $divisor = 10;
    $index = 100+$i;

    discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, 'netvision', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $volt);
  }
}

// EOF
