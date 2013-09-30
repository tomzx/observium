<?php

# FIXME Could perhaps do with a rewrite? with MIB?

if ($device['os'] == "papouch")
{
  echo("Papouch ");
  
  echo(" TME ");

  $descr = snmp_get($device, "1.3.6.1.4.1.18248.1.1.3.0", "-Oqv");
  $temperature = snmp_get($device, "1.3.6.1.4.1.18248.1.1.1.0", "-Oqv") / 10;

  if ($descr != "" && is_numeric($temperature) && $temperature > "0")
  {
    $temperature_oid = ".1.3.6.1.4.1.18248.1.1.1.0";
    $descr = trim(str_replace("\"", "", $descr));
    discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, "1", 'papouch-tme', $descr, 10, 1, NULL, NULL, NULL, NULL, $temperature);
  }

  echo(" TH2E ");

  $temperature = snmp_get($device, "1.3.6.1.4.1.18248.20.1.2.1.1.2.1", "-Oqv") / 10;

  if (is_numeric($temperature) && $temperature > "0")
  {
    if (snmp_get($device, "1.3.6.1.4.1.18248.20.1.3.1.1.1.1", "-Oqv"))
    {
      $low_limit = snmp_get($device, "1.3.6.1.4.1.18248.20.1.3.1.1.2.1", "-Oqv") / 10;
      $high_limit = NULL; # The MIB is invalid and I can't find the max value in snmpwalk :[ *sigh*
      # Hysteresis parameter value is in SNMPv2-SMI::enterprises.18248.20.1.3.1.1.3.1 = INTEGER: 100
    }
    else
    {
      $low_limit = NULL;
      $high_limit = NULL;
    }

    $temperature_oid = ".1.3.6.1.4.1.18248.20.1.2.1.1.2.1";
    discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, "1", 'papouch-th2e', "Temperature" , 10, 1, $low_limit, NULL, NULL, $high_limit, $temperature);
  }

  $temperature = snmp_get($device, "1.3.6.1.4.1.18248.20.1.2.1.1.2.3", "-Oqv") / 10;

  if (is_numeric($temperature) && $temperature > "0")
  {
    if (snmp_get($device, "1.3.6.1.4.1.18248.20.1.3.1.1.1.3", "-Oqv"))
    {
      $low_limit = snmp_get($device, "1.3.6.1.4.1.18248.20.1.3.1.1.2.3", "-Oqv") / 10;
      $high_limit = NULL; # The MIB is invalid and I can't find the max value in snmpwalk :[ *sigh*
      # Hysteresis parameter value is in SNMPv2-SMI::enterprises.18248.20.1.3.1.1.3.3 = INTEGER: 100
    }
    else
    {
      $low_limit = NULL;
      $high_limit = NULL;
    }

    $temperature_oid = ".1.3.6.1.4.1.18248.20.1.2.1.1.2.3";
    discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, "3", 'papouch-th2e', "Dew Point" , 10, 1, $low_limit, NULL, NULL, $high_limit, $temperature);
  }

  $humidity = snmp_get($device, "1.3.6.1.4.1.18248.20.1.2.1.1.2.1", "-Oqv") / 10;

  if (is_numeric($humidity) && $humidity > "0")
  {
    if (snmp_get($device, "1.3.6.1.4.1.18248.20.1.2.1.1.1.2", "-Oqv"))
    {
      $low_limit = snmp_get($device, "1.3.6.1.4.1.18248.20.1.3.1.1.2.2", "-Oqv") / 10;
      $high_limit = NULL; # The MIB is invalid and I can't find the max value in snmpwalk :[ *sigh*
      # Hysteresis parameter value is in SNMPv2-SMI::enterprises.18248.20.1.3.1.1.3.2 = INTEGER: 100
    }
    else
    {
      $low_limit = NULL;
      $high_limit = NULL;
    }

    $humidity_oid = ".1.3.6.1.4.1.18248.20.1.2.1.1.2.2";
    discover_sensor($valid['sensor'], 'humidity', $device, $humidity_oid, "1", 'papouch-th2e', "Humidity" , 10, 1, $low_limit, NULL, NULL, $high_limit, $humidity);
  }
}

// EOF
