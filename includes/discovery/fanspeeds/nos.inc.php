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
    if ($data)
    {
      list($oid) = explode(" ", $data);
      $fanspeed_oid  = "1.3.6.1.4.1.1588.2.1.1.1.1.22.1.4.$oid";
      $descr_oid = "1.3.6.1.4.1.1588.2.1.1.1.1.22.1.5.$oid";
      $descr = snmp_get($device,$descr_oid,"-Oqv");
      $speed = snmp_get($device,$fanspeed_oid,"-Oqv");
      if (strstr($descr, "FAN") && !strstr($speed, "No") && $descr != "" && $speed != "0")
      {
        $descr = str_replace("\"", "", $descr);
        $descr = str_replace("Speed", "", $descr);
        $descr = str_replace("Fan Speed", "", $descr);
        $descr = str_replace("sensor", "", $descr);
        $descr = trim($descr);

        discover_sensor($valid['sensor'], 'fanspeed', $device, $fanspeed_oid, $oid, '', $descr, '1', '1', NULL, NULL, NULL, NULL, $speed);
      }
    }
  }
}

?>
