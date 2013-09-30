<?php

# FIXME could do with a rewrite perhaps, with MIB and walk

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
      $sensor_oid  = "1.3.6.1.4.1.1588.2.1.1.1.1.22.1.4.$oid";
      $descr_oid = "1.3.6.1.4.1.1588.2.1.1.1.1.22.1.5.$oid";
      $descr = snmp_get($device,$descr_oid,"-Oqv");
      $current = snmp_get($device,$sensor_oid,"-Oqv");
      
      if (!strstr($current, "No") && $descr != "" && $current != "0")
      {
        $descr = str_replace("\"", "", $descr);
        $descr = str_replace("sensor", "", $descr);

        if (strstr($descr, "FAN"))
        {
          $descr = str_replace("Speed", "", $descr);
          $descr = str_replace("Fan Speed", "", $descr);
          $sensortype = 'fanspeed';
        }
        else if (strstr($descr, "TEMP"))
        {
          $descr = str_replace("temperature", "", $descr);
          $sensortype = 'temperature';
        }

        discover_sensor($valid['sensor'], $sensortype, $device, $sensor_oid, $oid, '', trim($descr), 1, 1, NULL, NULL, NULL, NULL, $current);
      }
    }
  }
}

// EOF
