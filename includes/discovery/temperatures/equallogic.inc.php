<?php

// EQLMEMBER-MIB

$eqlgrpmemid = get_dev_attrib($device, 'eqlgrpmemid');

if ($device['os'] == "equallogic" && (isset($eqlgrpmemid)))
{
  echo("EQLMEMBER-MIB ");
  $oids = snmpwalk_cache_oid($device, "eqlMemberHealthDetailsTemperatureTable", array(), "EQLMEMBER-MIB", mib_dirs("equallogic") );

  // copy of eqlMemberHealthDetailsTempSensorIndex
  $sensorname = array(
	"integratedSystemTemperature",
	"backplaneSensor0",
	"backplaneSensor1",
	"controlModule0processor",
	"controlModule0chipset",
	"controlModule1processor",
	"controlModule1chipset",
	"controlModule0sasController",
	"controlModule0sasExpander",
	"controlModule0sesEnclosure",
	"controlModule1sasController",
	"controlModule1sasExpander",
	"controlModule1sesEnclosure",
	"sesOpsPanel",
	"cemi0",
	"cemi1",
	"controlModule0batteryThermistor",
	"controlModule1batteryThermistor");
  $sensorid = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18);

  if (is_array($oids))
  {
    if ($debug) { print_vars($oids); }
    foreach ($oids as $index => $entry)
    {
      # EQLMEMBER-MIB returns sensors for all members. only process sensors that match our member id
      if (strstr($index, $eqlgrpmemid))
      {
      $numindex = str_replace($sensorname, $sensorid, $index);
      $entry['oid'] = ".1.3.6.1.4.1.12740.2.1.6.1.3.".$numindex;
      if ($entry['eqlMemberHealthDetailsTemperatureValue'] <> 0)
        {
        discover_sensor($valid['sensor'], 'temperature', $device, $entry['oid'], $numindex, 'equallogic',
          $entry['eqlMemberHealthDetailsTemperatureName'], '1', '1', 
          $entry['eqlMemberHealthDetailsTemperatureLowCriticalThreshold'], 
          $entry['eqlMemberHealthDetailsTemperatureLowWarningThreshold'], 
          $entry['eqlMemberHealthDetailsTemperatureHighCriticalThreshold'], 
          $entry['eqlMemberHealthDetailsTemperatureHighWarningThreshold'], 
          $entry['eqlMemberHealthDetailsTemperatureValue']);
        }
      }
    }
  }
}

?>

