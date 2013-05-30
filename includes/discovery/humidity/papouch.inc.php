<?php

if ($device['os'] == "papouch")
{
  echo("Papouch ");
  
  echo("TH2E ");

  $humidity = snmp_get($device, "SNMPv2-SMI::enterprises.18248.20.1.2.1.1.2.1", "-Oqv") / 10;

  if (is_numeric($humidity) && $humidity > "0")
  {
    if (snmp_get($device, "SNMPv2-SMI::enterprises.18248.20.1.2.1.1.1.2", "-Oqv"))
    {
      $low_limit = snmp_get($device, "SNMPv2-SMI::enterprises.18248.20.1.3.1.1.2.2", "-Oqv") / 10;
      $high_limit = NULL; # The MIB is invalid and I can't find the max value in snmpwalk :[ *sigh*
      # Hysteresis parameter value is in SNMPv2-SMI::enterprises.18248.20.1.3.1.1.3.2 = INTEGER: 100
    }
    else
    {
      $low_limit = NULL;
      $high_limit = NULL;
    }

    $humidity_oid = ".1.3.6.1.4.1.18248.20.1.2.1.1.2.2";
    discover_sensor($valid['sensor'], 'humidity', $device, $humidity_oid, "1", 'papouch-th2e', "Humidity" , '10', '1', $low_limit, NULL, NULL, $high_limit, $humidity);
  }
}

?>
