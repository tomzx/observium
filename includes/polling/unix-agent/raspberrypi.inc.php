<?php

/**
 * Observium
 *
 *   This files is part of Observium.
 *
 * @package    observium
 * @subpackage poller
 * @subpackage raspberrypi
 * @author     Dennis de Houx <info@all-in-one.be>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.0.1
 *
 */

global $agent_sensors;

if ($agent_data['raspberrypi'] != ':') {
  echo "raspberrypi: ";
  $rpiSensors = explode("\n", $agent_data['raspberrypi']);
  foreach ($rpiSensors as $item=>$rpiSensor) {
    $rpiSensor = trim($rpiSensor);
    if (!empty($rpiSensor)) {
      $data             = explode(":", $rpiSensor);
      list($type,$info) = explode("-", $data[0], 2);
      if ($type == "clock") {
        $clockcount++;
        $frequency = trim($data[1]);
        discover_sensor($valid['sensor'], 'frequency', $device, '', $clockcount, 'raspberrypi', $info, '1', '1', NULL, NULL, NULL, NULL, $frequency, 'agent');
        $agent_sensors['frequency']['raspberrypi'][$clockcount] = array('description' => $info, 'current' => $frequency, 'index' => $clockcount);
      }
      if ($type == "volts") {
        $voltcount++;
        $voltage = trim($data[1]);
        discover_sensor($valid['sensor'], 'voltage', $device, '', $voltcount, 'raspberrypi', $info, '1', '1', NULL, NULL, NULL, NULL, $voltage, 'agent');
        $agent_sensors['voltage']['raspberrypi'][$voltcount] = array('description' => $info, 'current' => $voltage, 'index' => $voltcount);
      }
      if ($type == "temp") {
        $tempcount++;
        $temprature = trim($data[1]);
        discover_sensor($valid['sensor'], 'temperature', $device, '', $tempcount, 'raspberrypi', 'raspberrypi', '1', '1', NULL, NULL, NULL, NULL, $temprature, 'agent');
        $agent_sensors['temperature']['raspberrypi'][$tempcount] = array('description' => 'raspberrypi', 'current' => $temprature, 'index' => $tempcount);
      }
    }
  }
  echo "\n";
  unset($clockcount);
  unset($voltcount);
  unset($tempcount);
  unset($frequency);
  unset($voltage);
  unset($temprature);
  unset($rpiSensors);
  unset($rpiSensor);
}

?>
