<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage discovery
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2012 Adam Armstrong
 *
 */

# MIB-Dell-10892::coolingDevicechassisIndex.1.1 = INTEGER: 1
# MIB-Dell-10892::coolingDeviceIndex.1.1 = INTEGER: 1
# MIB-Dell-10892::coolingDeviceStateCapabilities.1.1 = INTEGER: 0
# MIB-Dell-10892::coolingDeviceStateSettings.1.1 = INTEGER: enabled(2)
# MIB-Dell-10892::coolingDeviceStatus.1.1 = INTEGER: ok(3)
# MIB-Dell-10892::coolingDeviceReading.1.1 = INTEGER: 6240
# MIB-Dell-10892::coolingDeviceType.1.1 = INTEGER: coolingDeviceTypeIsAFan(3)
# MIB-Dell-10892::coolingDeviceLocationName.1.1 = STRING: "Fan 1"
# MIB-Dell-10892::coolingDeviceUpperCriticalThreshold.1.1 = INTEGER: 17040
# MIB-Dell-10892::coolingDeviceUpperNonCriticalThreshold.1.1 = INTEGER: 15000
# MIB-Dell-10892::coolingDeviceLowerNonCriticalThreshold.1.1 = INTEGER: 4320
# MIB-Dell-10892::coolingDeviceLowerCriticalThreshold.1.1 = INTEGER: 3720
# MIB-Dell-10892::coolingDeviceSubType.1.1 = INTEGER: coolingDeviceSubTypeIsAFanThatReadsInRPM(3)
# MIB-Dell-10892::coolingDeviceProbeCapabilities.1.1 = INTEGER: 15

if (strstr($device['hardware'], "Dell"))
{
  // stuff partially copied from akcp sensor
  $oids = snmp_walk($device, "coolingDeviceStateSetting", "-Osqn", "MIB-Dell-10892");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  if ($oids) echo("Dell OMSA ");
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$status) = explode(" ", $data, 2);
      if ($debug) { echo("status : ".$status."\n"); }
      if ($status == "enabled")
      {
        $split_oid        = explode('.',$oid);
        $fanspeed_id      = $split_oid[count($split_oid)-2].".".$split_oid[count($split_oid)-1];
        $descr_oid        = ".1.3.6.1.4.1.674.10892.1.700.12.1.8.$fanspeed_id";
        $fanspeed_oid     = ".1.3.6.1.4.1.674.10892.1.700.12.1.6.$fanspeed_id";
        $limit_oid        = ".1.3.6.1.4.1.674.10892.1.700.12.1.10.$fanspeed_id";
        $warnlimit_oid    = ".1.3.6.1.4.1.674.10892.1.700.12.1.11.$fanspeed_id";
        $lowwarnlimit_oid = ".1.3.6.1.4.1.674.10892.1.700.12.1.12.$fanspeed_id";
        $lowlimit_oid     = ".1.3.6.1.4.1.674.10892.1.700.12.1.13.$fanspeed_id";
        $subtype_oid      = ".1.3.6.1.4.1.674.10892.1.700.12.1.16.$fanspeed_id";

        $subtype      = snmp_get($device, $subtype_oid, "-Oqv", "MIB-Dell-10892");
        if (strstr($subtype, "RPM")) # exclude on/off fans; 1rpm doesn't make much sense :)
        {
          $descr        = trim(snmp_get($device, $descr_oid, "-Oqv", "MIB-Dell-10892"),'"');
          $fanspeed     = snmp_get($device, $fanspeed_oid, "-Oqv", "MIB-Dell-10892");
          $lowwarnlimit = snmp_get($device, $lowwarnlimit_oid, "-Oqv", "MIB-Dell-10892");
          if (empty($lowwarnlimit)) { $lowwarnlimit = null; }
          $warnlimit    = snmp_get($device, $warnlimit_oid, "-Oqv", "MIB-Dell-10892");
          if (empty($warnlimit)) { $warnlimit = 15000; } # value taken from PE1750
          $limit        = snmp_get($device, $limit_oid, "-Oqv", "MIB-Dell-10892");
          if (empty($limit)) { $limit = 17040; } # value taken from PE1750
          $lowlimit     = snmp_get($device, $lowlimit_oid, "-Oqv", "MIB-Dell-10892");
          if (empty($lowlimit)) { $lowlimit = null; }

          discover_sensor($valid['sensor'], 'fanspeed', $device, $fanspeed_oid, $fanspeed_id, 'dell', $descr, '1', '1', $lowlimit, $lowwarnlimit, $warnlimit, $limit, $fanspeed);
        }
      }
    }
  }
}

?>
