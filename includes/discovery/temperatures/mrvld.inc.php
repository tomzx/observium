<?php

if ($device['os'] == "mrvld")
{
  # OADWDM-MIB::oaLdCardTemp.1 = INTEGER: 20
  # OADWDM-MIB::oaLdCardTemp.2 = INTEGER: 0
  # OADWDM-MIB::oaLdCardType.1 = INTEGER: em2009gm2(35)
  # OADWDM-MIB::oaLdCardType.2 = INTEGER: empty(2)

  $oids = snmpwalk_cache_oid($device, "oaLdCardTemp", array(), "OADWDM-MIB");
  $oids = snmpwalk_cache_oid($device, "oaLdCardType", $oids, "OADWDM-MIB");

  if ($debug) { print_vars($oids); }

  if (is_array($oids))
  {
    echo("MRV LD-card Temperature ");
    foreach ($oids as $index => $entry)
    {
      if ($entry['oaLdCardType'] != 'empty')
      {
        $descr = "Slot ".$index . " " . $entry['oaLdCardType'];
        $oid = ".1.3.6.1.4.1.6926.1.41.3.1.1.26.".$index;
        $current = $entry['oaLdCardTemp'];

        if ($debug) { echo("descr: ".$descr." current: ".$current."\n"); }

        discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, 'lambdadriver', $descr, '1', '1', NULL, NULL, NULL, NULL, $current);
      }
    }
  }
}

?>
