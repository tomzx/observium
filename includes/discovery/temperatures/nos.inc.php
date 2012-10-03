<?php

if ($device['os'] == "nos" )
{
  echo("nos ");
  $oids = snmp_walk($device,"1.3.6.1.4.1.1588.2.1.1.1.1.22.1.4","-Osqn");
  $oids = trim($oids);
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    $data = substr($data, 35);
    $sensorid = explode(" ", $data);
    if ($data )
    {
      list($oid) = explode(" ", $data);
      $temperature_oid  = "1.3.6.1.4.1.1588.2.1.1.1.1.22.1.4.$oid";
      $descr_oid = "1.3.6.1.4.1.1588.2.1.1.1.1.22.1.5.$oid";
      $descr = snmp_get($device,$descr_oid,"-Oqv");
      $temperature = snmp_get($device,$temperature_oid,"-Oqv");
      if (strstr($descr, "TEMP") && !strstr($temperature, "No") && $descr != "" && $temperature != "0")
      {
        $descr = str_replace("\"", "", $descr);
        $descr = str_replace("temperature", "", $descr);
        $descr = str_replace("temperature", "", $descr);
        $descr = str_replace("sensor", "", $descr);
        $descr = trim($descr);

        discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, $oid, '', $descr, '1', '1', NULL, NULL, NULL, NULL, $temperature);
      }
    }
  }
}

?>
