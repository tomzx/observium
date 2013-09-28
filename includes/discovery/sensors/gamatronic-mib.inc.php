<?php

# FIXME - Needs a rewrite; discovered oids should not be textual, instead of for loops, use table walks if possible. -TL

if ($device['os'] == "gamatronicups")
{
  for ($i = 1; $i <= 3; $i++)
  {
    $current_oid = "GAMATRONIC-MIB::gamatronicLTD.5.4.1.1.3.$i";
    $descr = "Input Phase $i";
    $current = snmp_get($device, $current_oid, "-Oqv");
    $type = "gamatronicups";
    $precision = 1;
    $index = $i;
    $lowlimit = 0;
    $warnlimit = NULL;
    $limit = NULL;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, '1', '1', $lowlimit, NULL, NULL, NULL, $current);
  }

  for ($i = 1; $i <= 3; $i++)
  {
    $current_oid = "GAMATRONIC-MIB::gamatronicLTD.5.5.1.1.3.$i";
    $descr = "Output Phase $i";
    $current = snmp_get($device, $current_oid, "-Oqv");
    $type = "gamatronicups";
    $precision = 1;
    $index = 100+$i;
    $lowlimit = 0;
    $warnlimit = NULL;
    $limit = NULL;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, '1', '1', $lowlimit, NULL, NULL, NULL, $current);
  }

  for($i = 1; $i <= 3 ;$i++)
  {
    $volt_oid   = "GAMATRONIC-MIB::gamatronicLTD.5.4.1.1.2.$i";
    $descr = "Input Phase $i";
    $volt = snmp_get($device, $volt_oid, "-Oqv");
    $type = "gamatronicups";
    $divisor = 1;
    $index = $i;
    $lowlimit = 0;
    $limit = NULL;

    discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $volt);
  }

  for($i = 1; $i <= 3 ;$i++)
  {
    $volt_oid   = "GAMATRONIC-MIB::gamatronicLTD.5.5.1.1.2.$i";
    $descr = "Output Phase $i";
    $volt = snmp_get($device, $volt_oid, "-Oqv");
    $type = "gamatronicups";
    $divisor = 1;
    $index = 100+$i;
    $lowlimit = 0;
    $limit = NULL;

    discover_sensor($valid['sensor'], 'voltage', $device, $volt_oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $volt);
  }
}

// EOF
