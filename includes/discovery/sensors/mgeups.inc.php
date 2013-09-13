<?php

global $debug;

// MGE UPS
if ($device['os'] == "mgeups")
{
  echo("MG-SNMP-UPS-MIB ");

  $ups_array = array();
  $ups_array = snmpwalk_cache_multi_oid($device, "upsmgInputPhaseTable", $ups_array, "MG-SNMP-UPS-MIB");

  # Input
  $numPhase = snmp_get($device, "upsmgInputPhaseNum.0", "-Oqv", "MG-SNMP-UPS-MIB");

  # Great job MGE - my devices don't have mginputPhaseIndex, and mginputMinimumVoltage and mginputMaximumVoltage. are using different indexes.
  if (count(array_keys($ups_array)) > $numPhase) { unset($ups_array[0]); } # Remove [0] key with above 2 fields, leaving 1.0 etc for actual phases.

  foreach (array_keys($ups_array) as $index)
  {
    list($i,) = explode('.',$index,2);
    
    if ($i > $numPhase) { break; } # MGE returns 3 phase values even if their mgInputPhaseNum is 1. Doh.

    $phase = $ups_array[$index];

    $descr = "Input"; if ($numPhase > 1) { $descr .= " Phase $index"; }

    ## Input voltage
    $oid   = "1.3.6.1.4.1.705.1.6.2.1.2.$index"; # MG-SNMP-UPS-MIB:mginputVoltage.$index
    $value = $phase['mginputVoltage'] / 10;
    discover_sensor($valid['sensor'], 'voltage', $device, $oid, 100+$index, 'mge-ups', $descr, 10, 1, NULL, NULL, NULL, NULL, $value);

    ## Input current
    $oid   = "1.3.6.1.4.1.705.1.6.2.1.6.$index"; # MG-SNMP-UPS-MIB:mginputCurrent.$index
    $value = $phase['mginputCurrent'] / 10;
    discover_sensor($valid['sensor'], 'current', $device, $oid, 100+$index, 'mge-ups', $descr, 10, 1, NULL, NULL, NULL, NULL, $value);

    ## Input frequency
    $oid   = "1.3.6.1.4.1.705.1.6.2.1.3.$index"; # MG-SNMP-UPS-MIB:mginputFrequency.$index
    $value = $phase['mginputFrequency'] / 10;
    discover_sensor($valid['sensor'], 'frequency', $device, $oid, 100+$index, 'mge-ups', $descr, 10, 1, NULL, NULL, NULL, NULL, $value);
  }

  $ups_array = array();
  $ups_array = snmpwalk_cache_multi_oid($device, "upsmgOutputPhaseTable", $ups_array, "MG-SNMP-UPS-MIB");

  # Output
  $numPhase = snmp_get($device, "upsmgOutputPhaseNum.0", "-Oqv", "MG-SNMP-UPS-MIB");

  foreach ($ups_array as $phase)
  {
    $index = $phase['mgoutputPhaseIndex'];
    $descr = "Output"; if ($numPhase > 1) { $descr .= " Phase $index"; }

    # FIXME: Poll load: [mgoutputLoadPerPhase] => 18
    
    ## Output voltage
    $oid   = "1.3.6.1.4.1.705.1.7.2.1.2.$index"; # MG-SNMP-UPS-MIB:mgoutputVoltage.$index
    $value = $phase['mgoutputVoltage'] / 10;
    discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'mge-ups', $descr, 10, 1, NULL, NULL, NULL, NULL, $value);

    ## Output current
    $oid   = "1.3.6.1.4.1.705.1.7.2.1.5.$index"; # MG-SNMP-UPS-MIB:mgoutputCurrent.$index
    $value = $phase['mgoutputCurrent'] / 10;
    discover_sensor($valid['sensor'], 'current', $device, $oid, $index, 'mge-ups', $descr, 10, 1, NULL, NULL, NULL, NULL, $value);

    ## Output frequency
    $oid   = "1.3.6.1.4.1.705.1.7.2.1.3.$index"; # MG-SNMP-UPS-MIB:mgoutputFrequency.$index
    $value = $phase['mgoutputFrequency'] / 10;
    discover_sensor($valid['sensor'], 'frequency', $device, $oid, $index, 'mge-ups', $descr, 10, 1, NULL, NULL, NULL, NULL, $value);
  }

  # Environmental monitoring on UPSes etc
  // FIXME upsmgConfigEnvironmentTable and upsmgEnvironmentSensorTable are used but there are others ...
  $mge_env_data = snmpwalk_cache_oid($device, "upsmgConfigEnvironmentTable", array(), "MG-SNMP-UPS-MIB");
  $mge_env_data = snmpwalk_cache_oid($device, "upsmgEnvironmentSensorTable", $mge_env_data, "MG-SNMP-UPS-MIB");

/*
upsmgConfigSensorIndex.1 = 1
upsmgConfigSensorName.1 = "Environment sensor"
upsmgConfigTemperatureLow.1 = 5
upsmgConfigTemperatureHigh.1 = 40
upsmgConfigTemperatureHysteresis.1 = 2
upsmgConfigHumidityLow.1 = 5
upsmgConfigHumidityHigh.1 = 90
upsmgConfigHumidityHysteresis.1 = 5
upsmgConfigInput1Name.1 = "Input #1"
upsmgConfigInput1ClosedLabel.1 = "closed"
upsmgConfigInput1OpenLabel.1 = "open"
upsmgConfigInput2Name.1 = "Input #2"
upsmgConfigInput2ClosedLabel.1 = "closed"
upsmgConfigInput2OpenLabel.1 = "open"

upsmgEnvironmentIndex.1 = 1
upsmgEnvironmentComFailure.1 = no
upsmgEnvironmentTemperature.1 = 287
upsmgEnvironmentTemperatureLow.1 = no
upsmgEnvironmentTemperatureHigh.1 = no
upsmgEnvironmentHumidity.1 = 17
upsmgEnvironmentHumidityLow.1 = no
upsmgEnvironmentHumidityHigh.1 = no
upsmgEnvironmentInput1State.1 = open
upsmgEnvironmentInput2State.1 = open
*/

  foreach (array_keys($mge_env_data) as $index)
  {
    if ($mge_env_data[$index]['upsmgEnvironmentComFailure'] == 'no') # yes means no environment module present
    {
      $descr           = $mge_env_data[$index]['upsmgConfigSensorName'];
      $current         = $mge_env_data[$index]['upsmgEnvironmentHumidity'];
      $sensorType      = 'mge';
      $oid             = '.1.3.6.1.4.1.705.1.8.7.1.6.' . $index;
      $low_limit       = $mge_env_data[$index]['upsmgConfigHumidityLow'];
      $high_limit      = $mge_env_data[$index]['upsmgConfigHumidityHigh'];
      $hysteresis      = $mge_env_data[$index]['upsmgConfigHumidityHysteresis'];

      // FIXME warninglevels might need some other calculation instead of hysteresis
      $low_warn_limit  = $low_limit + $hysteresis;
      $high_warn_limit = $high_limit - $hysteresis;

      if ($debug) { echo("low_limit : $low_limit\nlow_warn_limit : $low_warn_limit\nhigh_warn_limit : $high_warn_limit\nhigh_limit : $high_limit\n"); }

      if ($current != 0)
      {
        # Humidity = 0 -> Sensor not available
        // FIXME true for MGE as wel as APC?
        discover_sensor($valid['sensor'], 'humidity', $device, $oid, $index, $sensorType, $descr, 1, 1, $low_limit, $low_warn_limit, $high_warn_limit, $high_limit , $current);
      }

      $current         = $mge_env_data[$index]['upsmgEnvironmentTemperature'];
      $sensorType      = 'mge';
      $oid             = '.1.3.6.1.4.1.705.1.8.7.1.3.' . $index;
      $low_limit       = $mge_env_data[$index]['upsmgConfigTemperatureLow'];
      $high_limit      = $mge_env_data[$index]['upsmgConfigTemperatureHigh'];
      $hysteresis      = $mge_env_data[$index]['upsmgConfigTemperatureHysteresis'];

      // FIXME warninglevels might need some other calculation instead of hysteresis
      $low_warn_limit  = $low_limit + $hysteresis;
      $high_warn_limit = $high_limit - $hysteresis;

      if ($debug) { echo("low_limit : $low_limit\nlow_warn_limit : $low_warn_limit\nhigh_warn_limit : $high_warn_limit\nhigh_limit : $high_limit\n"); }

      discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, $sensorType, $descr, 10, 1, $low_limit, $low_warn_limit, $high_warn_limit, $high_limit , $current/10);
    }
  }
}

//EOF