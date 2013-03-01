<?php

## OSPF-MIB::ospfNbrIpAddr.172.22.203.98.0

if($config['autodiscovery']['ospf'] != FALSE)
{

  echo("OSPF Neighbours: \n");

  $ips = snmpwalk_values($device, "OSPF-MIB::ospfNbrIpAddr", array(), "OSPF-MIB");

  foreach($ips as $ip)
  {
    $host = dbFetchRow("SELECT * FROM ipv4_addresses AS A, ports AS P, devices AS D WHERE A.ipv4_address = ? AND P.port_id = A.port_id AND D.device_id = P.device_id", array($ip));
    if(is_array($host))
    {
      echo("Already got $ip on ".$device['hostname']."\n");
    } else {
      discover_new_device_ip($ip);
    }
  }
}

?>
