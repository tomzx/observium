<?php

if (is_numeric($vars['id']))
{

  $ma = dbFetchRow("SELECT * FROM `mac_accounting` AS M, `ports` AS I, `devices` AS D WHERE M.ma_id = ? AND I.port_id = M.port_id AND I.device_id = D.device_id", array($vars['id']));

  if ($debug) {
    echo("<pre>");
    print_r($ma);
    echo("</pre>");
  }

  if (is_array($ma))
  {

    if ($auth || port_permitted($ma['port_id']))
    {

      $rrd_filename = $config['rrd_dir'] . "/" . $ma['hostname'] . "/" . safename("mac_acc-" . $ma['ifIndex'] . "-" . $ma['vlan_id'] ."-" . $ma['mac'] . ".rrd");
      if ($debug) { echo($rrd_filename); }

      if (is_file($rrd_filename))
      {
        if ($debug) { echo("exists"); }
        $port   = get_port_by_id($ma['port_id']);
        $device = device_by_id_cache($port['device_id']);
        $title  = generate_device_link($device);
        $title .= " :: Port  ".generate_port_link($port);
        $title .= " :: Mac Accounting";
        $title .= " :: " . formatMac($ma['mac']);
        $auth   = TRUE;
      } else {
   #     graph_error("file not found");
      }
    } else {
  #    graph_error("unauthenticated");
    }
  } else {
 #   graph_error("entry not found");
  }
} else {
#  graph_error("invalid id");
}
?>
