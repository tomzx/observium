<?php

if ($device['os'] == "wut")
{
  $oids = snmp_walk($device, ".1.3.6.1.4.1.5040.1.2.6.3.2.1.1.1", "-Osqn", "us_an8graph_mib_130.mib");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  if ($oids)
  {
    echo("Web-Thermograph:");
    foreach (explode("\n", $oids) as $data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.', $oid);
      $temperature_id = $split_oid[count($split_oid)-1];
      $temperature_oid = ".1.3.6.1.4.1.5040.1.2.6.1.3.1.1.$temperature_id";
      $temperature = snmp_get($device, $temperature_oid, "-Ovq");
      $descr = str_replace("\"", "", $descr);
      $descr = preg_replace('/Temperature  /', "", $descr);
      $descr = trim($descr);
        $warnlimit_oid    = ".1.3.6.1.4.1.5040.1.2.6.3.1.5.3.1.3.$temperature_id";
        $limit_oid        = ".1.3.6.1.4.1.5040.1.2.6.3.1.5.3.1.3.$temperature_id";
        $lowwarnlimit_oid = ".1.3.6.1.4.1.5040.1.2.6.3.1.5.3.1.2.$temperature_id";
        $lowlimit_oid     = ".1.3.6.1.4.1.5040.1.2.6.3.1.5.3.1.2.$temperature_id";

        $lowwarnlimit = floatval(trim(snmp_get($device, $lowwarnlimit_oid, "-Oqv", ""),'"'));
        $warnlimit    = floatval(trim(snmp_get($device, $warnlimit_oid, "-Oqv", ""),'"'));
        $limit        = floatval(trim(snmp_get($device, $limit_oid, "-Oqv", ""),'"'));
        $lowlimit     = floatval(trim(snmp_get($device, $lowlimit_oid, "-Oqv", ""),'"'));	
#	echo ("\n\rID:".$temperature_id."|OID:".$temperature_oid."|Temp:".$temperature."|lowwarnlimit:".$lowwarnlimit."|warnlimit:".$warnlimit."|limit:".$limit."|lowlimit:".$lowlimit);
        discover_sensor($valid['sensor'], 'temperature', $device, $temperature_oid, $temperature_id, 'wut', $descr, '1', '1', $lowlimit, $lowwarnlimit, $warnlimit, $limit, $temperature); 
    }
  }
}

?>
