<?php

if ($device['os'] == "linux")
{
  echo("LM-SENSORS-MIB ");

  $oids = snmp_walk($device, "lmTempSensorsDevice", "-Osqn", "LM-SENSORS-MIB");
  if ($debug) { echo($oids."\n"); }
  $divisor = 1000;

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $temperature_id = $split_oid[count($split_oid)-1];
      $temperature_oid  = "1.3.6.1.4.1.2021.13.16.2.1.3.$temperature_id";
      $temperature = snmp_get($device, $temperature_oid, "-Ovq") / $divisor;
      $descr = str_ireplace("temperature-", "", $descr);
      $descr = str_ireplace("temp-", "", $descr);
      $descr = trim($descr);
      if ($temperature != "0" && $temperature <= "1000")
      {
        discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, $temperature_id, 'lmsensors', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $temperature);
      }
    }
  }

  $oids = snmp_walk($device, "lmFanSensorsDevice", "-OsqnU", "LM-SENSORS-MIB");
  if ($debug) { echo($oids."\n"); }

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = "1.3.6.1.4.1.2021.13.16.3.1.3.". $index;
      $current = snmp_get($device, $oid, "-Oqv", "LM-SENSORS-MIB");
      $descr = trim(str_ireplace("fan-", "", $descr));
      if ($current > '0')
      {
        discover_sensor($valid['sensor'], 'fanspeed', $device, $oid, $index, 'lmsensors', $descr, '1', '1', NULL, NULL, NULL, NULL, $current);
      }
    }
  }

  $oids = snmp_walk($device, "lmVoltSensorsDevice", "-OsqnU", "LM-SENSORS-MIB");
  if ($debug) { echo($oids."\n"); }
  $divisor = 1000;

  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = "1.3.6.1.4.1.2021.13.16.4.1.3." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "LM-SENSORS-MIB") / $divisor;

      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'lmsensors', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  }
}

?>
