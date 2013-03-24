<?php

echo("Mac Accounting: ");

// FIXME -- we're walking, so we can discover here too.

// Cisco MAC Accounting

if ($device['os_group'] == "cisco")
{

  echo("Cisco ");

  $sql  = "SELECT *, `mac_accounting`.`ma_id` as `ma_id`";
  $sql .= " FROM  `mac_accounting`";
  $sql .= " LEFT JOIN  `mac_accounting-state` ON  `mac_accounting`.ma_id =  `mac_accounting-state`.ma_id";
  $sql .= " WHERE `device_id` = ?";
  $arg  = array($device['device_id']);

  foreach (dbFetchRows($sql, $arg) as $acc)
  {
    $port = get_port_by_id($acc['port_id']);
    $acc['ifIndex'] = $port['ifIndex'];
    unset($port);
    $ma_db[$acc['ifIndex']][$acc['mac']] = $acc;
  }

  if($debug) { print_r($ma_db); }

  $datas = snmp_walk($device, "CISCO-IP-STAT-MIB::cipMacSwitchedBytes", "-OUqsX", "NS-ROOT-MIB");
  $datas .= "\n".snmp_walk($device, "CISCO-IP-STAT-MIB::cipMacSwitchedPkts", "-OUqsX", "NS-ROOT-MIB");

  foreach (explode("\n", $datas) as $data) {
    list($oid,$ifIndex,$dir,$mac,$value) = parse_oid2($data);
    list($a_a, $a_b, $a_c, $a_d, $a_e, $a_f) = explode(":", $mac);
    $ah_a = zeropad($a_a);
    $ah_b = zeropad($a_b);
    $ah_c = zeropad($a_c);
    $ah_d = zeropad($a_d);
    $ah_e = zeropad($a_e);
    $ah_f = zeropad($a_f);
    $mac = "$ah_a$ah_b$ah_c$ah_d$ah_e$ah_f";

    $oid = str_replace(array("cipMacSwitchedBytes", "cipMacSwitchedPkts"), array("bytes", "pkts"), $oid);
    $ma_array[$ifIndex][$mac][$oid][$dir] = $value;
  }

  if($debug) { print_r($ma_array); }

}

// Below this should be MIB / vendor agnostic.

if(count($ma_array))
{

  $polled = time();
  $mac_entries = 0;

  foreach (dbFetchRows($sql, $arg) as $acc)
  {
    if($debug) { print_r($acc); }
    $port = get_port_by_id($acc['port_id']);
    $device_id = $port['device_id'];
    $ifIndex = $port['ifIndex'];
    $mac = $acc['mac'];
    $polled_period = $polled - $acc['poll_time'];

    if($debug) { print_r($ma_array[$ifIndex][$mac]); }

    if ($ma_array[$ifIndex][$mac])
    {
      $acc['update']['poll_time'] = $polled;
      $acc['update']['poll_period'] = $polled_period;
      $mac_entries++;
      $b_in = $ma_array[$ifIndex][$mac]['bytes']['input'];
      $b_out = $ma_array[$ifIndex][$mac]['bytes']['output'];
      $p_in = $ma_array[$ifIndex][$mac]['pkts']['input'];
      $p_out = $ma_array[$ifIndex][$mac]['pkts']['output'];

      $this_ma = &$ma_array[$ifIndex][$mac];

      echo(" ".$port['ifDescr']."(".$ifIndex.") -> ".$mac);

      // Update metrics
      foreach (array('bytes','pkts') as $oid)
      {
        foreach (array('input','output') as $dir)
        {
          $oid_dir = $oid . "_" . $dir;
          $acc['update'][$oid_dir] = $this_ma[$oid][$dir];
          if ($this_ma[$oid][$dir])
          {
            $oid_diff = $this_ma[$oid][$dir] - $acc[$oid_dir];
            $oid_rate  = $oid_diff / $polled_period;
            $acc['update'][$oid_dir.'_rate'] = $oid_rate;
            $acc['update'][$oid_dir.'_delta'] = $oid_diff;
            if ($debug) { echo("\n $oid_dir ($oid_diff B) $oid_rate Bps $polled_period secs\n"); }
          }
        }
      }

      if ($debug) { echo("\n" . $acc['hostname']." ".$acc['ifDescr'] . "  $mac -> $b_in:$b_out:$p_in:$p_out "); }

      $rrdfile = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("mac_acc-" . $port['ifIndex'] . "-" . $acc['mac'] . ".rrd");

      if (!is_file($rrdfile))
      {
        rrdtool_create($rrdfile,"DS:IN:COUNTER:600:0:12500000000 \
          DS:OUT:COUNTER:600:0:12500000000 \
          DS:PIN:COUNTER:600:0:12500000000 \
          DS:POUT:COUNTER:600:0:12500000000 " . $config['rrd_rra']);
      }

      // FIXME - use memory tables to make sure these values don't go backwards?
      $rrdupdate = array($b_in, $b_out, $p_in, $p_out);
      rrdtool_update($rrdfile, $rrdupdate);

      if ($acc['update'])
      { // Do Updates
        if (empty($acc['poll_time']))
        {
          $insert = dbInsert(array('ma_id' => $acc['ma_id']), 'mac_accounting-state');
          if ($debug) { echo("state inserted"); }
        }
        dbUpdate($acc['update'], 'mac_accounting-state', '`ma_id` = ?', array($acc['ma_id']));
        if ($debug) { echo("state updated"); }
      } // End Updates
    }
  }

  unset($ma_array);

  if ($mac_entries) { echo(" $mac_entries MAC accounting entries\n"); }

  echo("\n");
}

echo("\n");

?>
