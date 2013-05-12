<?php

// Generate some statistics to send along with the version request.
//
// These stats are used to allow us to prioritise development resources
// to target features and devices that are used the most.

// Overall Ports/Devices statistics
$stats['ports']          = dbFetchCell("SELECT count(*) FROM ports");
$stats['devices']        = dbFetchCell("SELECT count(*) FROM devices");

// Per-feature statistics
$stats['sensors']        = dbFetchCell("SELECT count(*) FROM sensors");
$stats['services']       = dbFetchCell("SELECT count(*) FROM services");
$stats['applications']   = dbFetchCell("SELECT count(*) FROM applications");
$stats['bgp']            = dbFetchCell("SELECT count(*) FROM bgpPeers");
$stats['ospf']           = dbFetchCell("SELECT count(*) FROM ospf_ports");
$stats['eigrp']          = dbFetchCell("SELECT count(*) FROM eigrp_ports");
$stats['ipsec_tunnels']  = dbFetchCell("SELECT count(*) FROM ipsec_tunnels");
$stats['munin_plugins']  = dbFetchCell("SELECT count(*) FROM munin_plugins");
$stats['pseudowires']    = dbFetchCell("SELECT count(*) FROM pseudowires");
$stats['vrfs']           = dbFetchCell("SELECT count(*) FROM vrfs");
$stats['vminfo']         = dbFetchCell("SELECT count(*) FROM vminfo");
$stats['users']          = dbFetchCell("SELECT count(*) FROM users");

// sysObjectID for Generic devices
foreach (dbFetch("SELECT sysObjectID, COUNT( * ) as count FROM  `devices` WHERE `os` = 'generic' GROUP BY `sysObjectID`") as $gendata)
{
  $stats['generics'][$gendata['sysObjectID']] = $gendata['count'];
}

// Per-OS counts
foreach (dbFetch("SELECT COUNT(*) AS count,os from devices group by `os`") as $dt_data)
{
  $stats['devicetypes'][$dt_data['os']] = $dt_data['count'];
}

$stat_serial = base64_encode(serialize($stats));

$url = "http://update.observium.org/latest.php?i=".$stats['ports']."&d=".$stats['devices']."&stats=".$stat_serial."&v=".$config['version']."&gen_serial=".$gen_serial;
$dataHandle = fopen($url, r);

if ($dataHandle)
{
  while (!feof($dataHandle))
  {
    $data.= fread($dataHandle, 4096);
  }
  if ($data)
  {
    list($omnipotence, $year, $month, $revision) = explode(".", $data);
    list($cur, $tag) = explode("-", $config['version']);
    list($cur_omnipotence, $cur_year, $cur_month, $cur_revision) = explode(".", $cur);

    if ($argv[1] == "--cron" || isset($options['q']))
    {
      $fd = fopen($config['log_file'],'a');
      fputs($fd,$string . "\n");
      fclose($fd);

      shell_exec("echo $omnipotence.$year.$month.$month > ".$config['rrd_dir']."/version.txt ");
    } else {
      if ($cur != $data)
      {
        echo("Current Revision : $cur_revision\n");

        if ($revision > $cur_revision)
        {
          echo("New Revision   : $revision\n");
        }

#        if ($omnipotence > $cur_omnipotence)
#        {
#          echo("New version     : $omnipotence.$year.$month.$revision\n");
#        } elseif ($year > $cur_year) {
#          echo("New version     : $omnipotence.$year.$month.$revision\n");
#        } elseif ($month > $cur_month) {
#          echo("New version     : $omnipotence.$year.$month.$revision\n");
#        } elseif ($revision > $cur_revision) {
#          echo("New release     : $omnipotence.$year.$month.$revision\n");
#        }
      }
    }
  }

  fclose($dataHandle);
}

?>
