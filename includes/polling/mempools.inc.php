<?php

$sql  = "SELECT *, `mempools`.mempool_id as mempool_id";
$sql .= " FROM  `mempools`";
$sql .= " LEFT JOIN  `mempools-state` ON  `mempools`.mempool_id =  `mempools-state`.mempool_id";
$sql .= " WHERE `device_id` = ?";

foreach (dbFetchRows($sql, array($device['device_id'])) as $mempool)
{
  echo("Mempool ". $mempool['mempool_descr'] . ": ");

  $mempool_rrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("mempool-" . $mempool['mempool_type'] . "-" . $mempool['mempool_index'] . ".rrd");

  $file = $config['install_dir']."/includes/polling/mempools/".$mempool['mempool_type'].".inc.php";
  if (is_file($file))
  {
    include($file);
  } else {
    // Do we need a generic mempool poller?
  }

  if ($mempool['total'])
  {
    $percent = round($mempool['used'] / $mempool['total'] * 100, 2);
  }
  else
  {
    $percent = 0;
  }

  $percent = round($percent, 2);

  echo($percent."% ");

  // Update StatsD/Carbon
  if($config['statsd']['enable'] == TRUE)
  {
    StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'mempool'.'.'.$mempool['mempool_type'] . "." . $mempool['mempool_index'].".used", $mempool['used']);
    StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'mempool'.'.'.$mempool['mempool_type'] . "." . $mempool['mempool_index'].".free", $mempool['free']);
  }



  if (!is_file($mempool_rrd))
  {
   rrdtool_create($mempool_rrd, "--step 300 DS:used:GAUGE:600:0:U DS:free:GAUGE:600:0:U ".$config['rrd_rra']);
  }
  rrdtool_update($mempool_rrd,"N:".$mempool['used'].":".$mempool['free']);

  if (!is_numeric($mempool['mempool_polled'])) { dbInsert(array('mempool_id' => $mempool['mempool_id']), 'mempools-state'); }

  $mempool['state'] = array('mempool_polled' => time(), 'mempool_used' => $mempool['used'], 'mempool_perc' => $percent, 'mempool_free' => $mempool['free'],
                 'mempool_total' => $mempool['total'], 'mempool_largestfree' => $mempool['largestfree'], 'mempool_lowestfree' => $mempool['lowestfree']);

  dbUpdate($mempool['state'], 'mempools-state', '`mempool_id` = ?', array($mempool['mempool_id']));
  $graphs['mempool'] = TRUE;

  check_entity('mempool', $mempool, array('mempool_perc' => $percent));

  echo("\n");
}

unset($mempool_cache);

?>
