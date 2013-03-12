<?php

if ($device['os'] == "arista_eos")
{
  echo("ARISTA-SW-IP-FORWARDING\n");

  $data = snmpwalk_cache_oid($device, "aristaSwFwdIpStatsTable", array(), "ARISTA-SW-IP-FORWARDING-MIB");
  $oids = array ('HCInReceives', 'InHdrErrors', 'InNoRoutes', 'InAddrErrors',
                 'InUnknownProtos', 'InTruncatedPkts',
                 'HCInForwDatagrams',
                 'ReasmReqds', 'ReasmOKs', 'ReasmFails',
                 'OutNoRoutes', 'HCOutForwDatagrams',
                 'OutDiscards',
                 'OutFragReqds', 'OutFragOKs', 'OutFragFails', 'OutFragCreates',
                 'HCOutTransmits' );

  $rrdfile = $config['rrd_dir'] . "/" . $device['hostname'] . "/arista-netstats-sw-ip.rrd";
  $rrdfile6 = $config['rrd_dir'] . "/" . $device['hostname'] . "/arista-netstats-sw-ip6.rrd";

  $rrd_create = "RRA:AVERAGE:0.5:1:600 RRA:AVERAGE:0.5:6:700 RRA:AVERAGE:0.5:24:775 RRA:AVERAGE:0.5:288:797 RRA:MAX:0.5:1:600 RRA:MAX:0.5:6:700 RRA:MAX:0.5:24:775 RRA:MAX:0.5:288:797";
  foreach ($oids as $oid)
  {
    $oid_ds = str_replace("HC", "", $oid);
    $rrd_create .= " DS:$oid_ds:COUNTER:600:U:100000000000";
  }

  $have6 = isset( $data['ipv6'] );
  $rrdupdate = "N";
  $rrdupdate6 = "N";
  foreach ($oids as $oid) {
    $rrdupdate .= ":" .$data[ 'ipv4' ][ 'aristaSwFwdIpStats' . $oid ];
    if ($have6) {
      $rrdupdate6 .= ":" .$data[ 'ipv6' ][ 'aristaSwFwdIpStats' . $oid ];
    }
  }
  if (!file_exists($rrdfile)) { rrdtool_create($rrdfile,$rrd_create); }
  rrdtool_update($rrdfile, $rrdupdate);
  if ($have6) {
    if (!file_exists($rrdfile6)) { rrdtool_create($rrdfile6,$rrd_create); }
    rrdtool_update($rrdfile6, $rrdupdate6);
  }

  unset($data, $oid, $oids, $oid_ds, $rrdfile, $rrdupate, $rrd_create);

  $graphs['netstat_arista_sw_ip'] = TRUE;
  $graphs['netstat_arista_sw_ip_frag'] = TRUE;
  if ($have6) {
    $graphs['netstat_arista_sw_ip6'] = TRUE;
    $graphs['netstat_arista_sw_ip6_frag'] = TRUE;
  }
}

?>
