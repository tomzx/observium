<?php

if($device['os'] != "junos")
{

echo(" ENTITY-SENSOR: ");

echo("Caching OIDs:");

if (!is_array($entity_array))
{
  $entity_array = array();
  echo(" entPhysicalDescr");
  $entity_array = snmpwalk_cache_multi_oid($device, "entPhysicalDescr", $entity_array, "CISCO-ENTITY-SENSOR-MIB");
  echo(" entPhysicalName");
  $entity_array = snmpwalk_cache_multi_oid($device, "entPhysicalName", $entity_array, "CISCO-ENTITY-SENSOR-MIB");
}

$oids = array();
echo(" entPhySensorType");
$oids = snmpwalk_cache_multi_oid($device, "entPhySensorType", $oids, "ENTITY-SENSOR-MIB");
echo(" entPhySensorScale");
$oids = snmpwalk_cache_multi_oid($device, "entPhySensorScale", $oids, "ENTITY-SENSOR-MIB");
echo(" entPhySensorPrecision");
$oids = snmpwalk_cache_multi_oid($device, "entPhySensorPrecision", $oids, "ENTITY-SENSOR-MIB");
echo(" entPhySensorValue");
$oids = snmpwalk_cache_multi_oid($device, "entPhySensorValue", $oids, "ENTITY-SENSOR-MIB");
if ($device['os'] == 'arista_eos')
{
  foreach ( array('aristaEntSensorThresholdLowWarning',
             'aristaEntSensorThresholdLowCritical',
             'aristaEntSensorThresholdHighWarning',
             'aristaEntSensorThresholdHighCritical') as $column ) {
    echo(" " . $column);
    $oids = snmpwalk_cache_multi_oid($device, $column, $oids, "ARISTA-ENTITY-SENSOR-MIB", mib_dirs('arista'));
  }
}

$entitysensor['voltsDC']   = "voltage";
$entitysensor['voltsAC']   = "voltage";
$entitysensor['amperes']   = "current";
$entitysensor['watts']     = "power";
$entitysensor['hertz']     = "freq";
$entitysensor['percentRH'] = "humidity";
$entitysensor['rpm']       = "fanspeed";
$entitysensor['celsius']   = "temperature";
$entitysensor['dBm']       = "dbm";

if (is_array($oids))
{
  foreach ($oids as $index => $entry)
  {
    #echo("[" . $entry['entPhySensorType'] . "|" . $entry['entPhySensorValue']. "|" . $index . "]");

    if ($entitysensor[$entry['entPhySensorType']] && is_numeric($entry['entPhySensorValue']) && is_numeric($index))
    {
      $entPhysicalIndex = $index;
      $oid = ".1.3.6.1.2.1.99.1.1.1.4.".$index;
      $current = $entry['entPhySensorValue'];
      #ENTITY-SENSOR-MIB::entPhySensorUnitsDisplay.11 = STRING: "C"

      $descr = $entity_array[$index]['entPhysicalName'];
#      if ($descr || $device['os'] == "iosxr")
      if ($descr)
      {
        $descr = rewrite_entity_name($descr);
      } else {
        $descr = $entity_array[$index]['entPhysicalDescr'];
        $descr = rewrite_entity_name($descr);
      }

      $thisisnotbullshit = TRUE;

      $type = $entitysensor[$entry['entPhySensorType']];

      // FIXME this stuff is foul

      if ($entry['entPhySensorScale'] == "nano")  { $divisor = "1000000000"; $multiplier = "1"; }
      if ($entry['entPhySensorScale'] == "micro") { $divisor = "1000000"; $multiplier = "1"; }
      if ($entry['entPhySensorScale'] == "milli") { $divisor = "1000"; $multiplier = "1"; }
      if ($entry['entPhySensorScale'] == "units") { $divisor = "1"; $multiplier = "1"; }
      if ($entry['entPhySensorScale'] == "kilo")  { $divisor = "1"; $multiplier = "1000"; }
      if ($entry['entPhySensorScale'] == "mega")  { $divisor = "1"; $multiplier = "1000000"; }
      if ($entry['entPhySensorScale'] == "giga")  { $divisor = "1"; $multiplier = "1000000000"; }

      if (is_numeric($entry['entPhySensorPrecision']) && $entry['entPhySensorPrecision'] > "0") { $divisor = $divisor . str_pad('', $entry['entPhySensorPrecision'], "0"); }
      $current = $current * $multiplier / $divisor;

      if ($type == "temperature") { if ($current > "200") { $thisisnotbullshit = FALSE; } $descr = preg_replace("/[T|t]emperature[|s]/", "", $descr); }

      #echo($descr . "|" . $index . "|" .$current . "|" . $multiplier . "|" . $divisor ."|" . $entry['entPhySensorScale'] . "|" . $entry['entPhySensorPrecision'] . "\n");

      if ($current == "-127") { $thisisnotbullshit = FALSE; }

      $lowlimit = NULL;
      $lowwarn = NULL;
      $highwarn = NULL;
      $highlimit = NULL;
      if ($device['os'] == 'arista_eos') {
        $lowlimit = $entry['aristaEntSensorThresholdLowCritical'];
        $lowwarn = $entry['aristaEntSensorThresholdLowWarning'];
        $highwarn = $entry['aristaEntSensorThresholdHighWarning'];
        $highlimit = $entry['aristaEntSensorThresholdHighCritical'];
        $lowlimit = $lowlimit * $multiplier / $divisor;
        $lowwarn = $lowwarn * $multiplier / $divisor;
        $highwarn = $highwarn * $multiplier / $divisor;
        $highlimit = $highlimit * $multiplier / $divisor;
        # FIXME: The MIB can return -1000000000 or +1000000000, if there
        # should be no threshold there.  We don't use NULL in that case
        # because observium then calculates its own threshold using
        # sensor_limit() or sensor_low_limit(), but instead it should
        # have a flag value for "the device has no limit for this sensor".
      }

      if ($thisisnotbullshit && mysql_result(mysql_query("SELECT COUNT(*) FROM `sensors` WHERE `device_id` = '".$device['device_id']."' AND `sensor_class` = '".$type."' AND `sensor_type` = 'cisco-entity-sensor' AND `sensor_index` = '".$index."'"),0) == "0")
      // Check to make sure we've not already seen this sensor via cisco's entity sensor mib
      {
        discover_sensor($valid['sensor'], $type, $device, $oid, $index, 'entity-sensor', $descr, $divisor, $multiplier, $lowlimit, $lowwarn, $highwarn, $highlimit, $current);
      }
    }
  }
}

echo("\n");

}
?>
