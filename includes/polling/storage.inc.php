<?php

$storage_cache = array();

$sql  = "SELECT `storage`.*, `storage-state`.storage_polled";
$sql .= " FROM  `storage`";
$sql .= " LEFT JOIN `storage-state` ON `storage`.storage_id = `storage-state`.storage_id";
$sql .= " WHERE `device_id` = ?";

foreach (dbFetchRows($sql, array($device['device_id'])) as $storage)
{
  echo("Storage ".$storage['storage_descr'] . ": ");

  $storage_rrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("storage-" . $storage['storage_mib'] . "-" . safename($storage['storage_descr']) . ".rrd");

  if (!is_file($storage_rrd))
  {
   rrdtool_create($storage_rrd, " DS:used:GAUGE:600:0:U DS:free:GAUGE:600:0:U ");
  }

  $file = $config['install_dir']."/includes/polling/storage-".$storage['storage_mib'].".inc.php";
  if (is_file($file))
  {
    include($file);
  } else {
    // Generic poller goes here if we ever have a discovery module which uses it.
  }

  if ($debug) {print_vars($storage); }

  if ($storage['size'])
  {
    $percent = round($storage['used'] / $storage['size'] * 100);
  }
  else
  {
    $percent = 0;
  }

  echo($percent."% ");

  // Update StatsD/Carbon
  if($config['statsd']['enable'] == TRUE)
  {
    StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'storage'.'.' .$storage['storage_mib'] . "-" . safename($storage['storage_descr']).".used", $storage['used']);
    StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'storage'.'.' .$storage['storage_mib'] . "-" . safename($storage['storage_descr']). ".free", $storage['free']);
  }

  // Update RRD
  rrdtool_update($storage_rrd,"N:".$storage['used'].":".$storage['free']);

  if (!is_numeric($storage['storage_polled'])) { dbInsert(array('storage_id' => $storage['storage_id'], 'storage_used' => $storage['used'],
    'storage_free' => $storage['free'], 'storage_size' => $storage['size'], 'storage_units' => $storage['units'], 'storage_perc' => $percent), 'storage-state'); }

  $update = dbUpdate(array('storage_polled' => time(), 'storage_used' => $storage['used'], 'storage_free' => $storage['free'], 'storage_size' => $storage['size'],
    'storage_units' => $storage['units'], 'storage_perc' => $percent), 'storage-state', '`storage_id` = ?', array($storage['storage_id']));
  $graphs['storage'] = TRUE;

  // Check alerts

  check_entity('storage', $mempool, array('storage_perc' => $percent));


  echo("\n");
}

unset($storage);

?>
