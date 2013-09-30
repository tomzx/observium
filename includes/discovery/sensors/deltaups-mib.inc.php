<?php

// DeltaUPS-MIB
if ($device['os'] == "deltaups")
{
  echo("DeltaUPS-MIB ");

  $dupsSensors = array(
    array('OID' => "1.3.6.1.4.1.2254.2.4.7.7.0",  'descr' => "Battery",     'divisor' => 1,  'class' => 'current'),     # dupsBatteryCurrent.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.5.5.0",  'descr' => "Output",      'divisor' => 10, 'class' => 'current'),     # dupsOutputCurrent1.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.4.4.0",  'descr' => "Input",       'divisor' => 10, 'class' => 'current'),     # dupsInputCurrent1.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.6.4.0",  'descr' => "Bypass",      'divisor' => 10, 'class' => 'current'),     # dupsBypassCurrent1.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.5.2.0",  'descr' => "Output",      'divisor' => 10, 'class' => 'frequency'),   # dupsOutputFrequency.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.4.2.0",  'descr' => "Input",       'divisor' => 10, 'class' => 'frequency'),   # dupsInputFrequency.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.10.2.0", 'descr' => "Environment", 'divisor' => 1,  'class' => 'humidity'),    # dupsEnvHumidity.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.10.1.0", 'descr' => "Environment", 'divisor' => 1,  'class' => 'temperature'), # dupsEnvTemperature.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.7.9.0",  'descr' => "Battery",     'divisor' => 1,  'class' => 'temperature'), # dupsTemperature.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.7.6.0",  'descr' => "Battery",     'divisor' => 10, 'class' => 'voltage'),     # dupsBatteryVoltage.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.5.4.0",  'descr' => "Output",      'divisor' => 10, 'class' => 'voltage'),     # dupsOutputVoltage1.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.4.3.0",  'descr' => "Input",       'divisor' => 10, 'class' => 'voltage'),     # dupsInputVoltage1.0
    array('OID' => "1.3.6.1.4.1.2254.2.4.6.3.0",  'descr' => "Bypass",      'divisor' => 10, 'class' => 'voltage'),     # dupsBypassVoltage1.0
  );
  
  //FIXME - This only discovers a single phase - probably needs more values above? ie dupsBypassVoltage1.0 is polled, dupsBypassVoltage2.0 and 3.0 aren't, etc.

  foreach ($dupsSensors as $eachArray => $eachValue)
  {
    // DeltaUPS does not have tables, so no need to walk, only need snmpget
    $value = snmp_get($device, $eachValue['OID'], "-O vq");
    // Get index values from current OID
    $preIndex = strstr($eachValue['OID'], '2254.2.4');
    // Format and strip index to only include everything after 2254.2.4
    $index = substr($preIndex, 9);

    // Prevent NULL returned values from being added as sensors
    if ($value != "NULL")
    {
      discover_sensor($valid['sensor'], $eachValue['class'], $device, $eachValue['OID'], $index, "DeltaUPS", $eachValue['descr'], $eachValue['divisor'], 1, NULL, NULL, NULL, NULL, $value);
    }
  }
}
