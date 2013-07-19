<?php

// Parse output of ipmitool sensor
function parse_ipmitool_sensor($device, $results, $source = 'ipmi')
{
  global $valid, $config;

  $index = 0;

  foreach (explode("\n",$results) as $row)
  {
    $index++;

    # BB +1.1V IOH     | 1.089      | Volts      | ok    | na        | 1.027     | 1.054     | 1.146     | 1.177     | na
    list($desc,$current,$unit,$state,$low_nonrecoverable,$low_limit,$low_warn,$high_warn,$high_limit,$high_nonrecoverable) = explode('|',$row);

    if (trim($current) != "na" && $config['ipmi_unit'][trim($unit)])
    {
      discover_sensor($valid['sensor'], $config['ipmi_unit'][trim($unit)], $device, '', $index, $source, trim($desc), '1', '1',
        (trim($low_limit) == 'na' ? NULL : trim($low_limit)), (trim($low_warn) == 'na' ? NULL : trim($low_warn)),
        (trim($high_warn) == 'na' ? NULL : trim($high_warn)), (trim($high_limit) == 'na' ? NULL : trim($high_limit)),
        $current, $source);

      $ipmi_sensors[$config['ipmi_unit'][trim($unit)]][$source][$index] = array('description' => $desc, 'current' => $current, 'index' => $index, 'unit' => $unit);
    }
  }

  return $ipmi_sensors;
}

// Poll a sensor
function poll_sensor($device, $class, $unit, &$oid_cache)
{
  global $config, $agent_sensors, $ipmi_sensors;

  $sql  = "SELECT *, `sensors`.`sensor_id` AS `sensor_id`";
  $sql .= " FROM  `sensors`";
  $sql .= " LEFT JOIN  `sensors-state` ON  `sensors`.sensor_id =  `sensors-state`.sensor_id";
  $sql .= " WHERE `sensor_class` = ? AND `device_id` = ?";

  foreach (dbFetchRows($sql, array($class, $device['device_id'])) as $sensor)
  {
    echo("Checking (" . $sensor['poller_type'] . ") $class " . $sensor['sensor_descr'] . " ");

    if ($sensor['poller_type'] == "snmp")
    {
      # if ($class == "temperature" && $device['os'] == "papouch")
      // Why all temperature?
      if ($class == "temperature")
      {
        for ($i = 0;$i < 5;$i++) # Try 5 times to get a valid temp reading
        {
          if ($debug) echo("Attempt $i ");
          // Take value from $oid_cache if we have it, else snmp_get it
          if(is_numeric($oid_cache[$sensor['sensor_oid']]))
          {
            if ($debug) echo("value taken from oid_cache ");
            $sensor_value = $oid_cache[$sensor['sensor_oid']];
          } else {
            $sensor_value =  preg_replace("/[^0-9.]/", "", snmp_get($device, $sensor['sensor_oid'], "-OUqnv", "SNMPv2-MIB"));
          }

          if (is_numeric($sensor_value) && $sensor_value != 9999) break; # TME sometimes sends 999.9 when it is right in the middle of an update;
          sleep(1); # Give the TME some time to reset
        }
      } else {
        // Take value from $oid_cache if we have it, else snmp_get it
        if(is_numeric($oid_cache[$sensor['sensor_oid']]))
        {
          if ($debug) echo("value taken from oid_cache ");
          $sensor_value = $oid_cache[$sensor['sensor_oid']];
        } else {
          $sensor_value = trim(str_replace("\"", "", snmp_get($device, $sensor['sensor_oid'], "-OUqnv", "SNMPv2-MIB")));
        }
      }
    } else if ($sensor['poller_type'] == "agent")
    {
      if (isset($agent_sensors))
      {
        $sensor_value = $agent_sensors[$class][$sensor['sensor_type']][$sensor['sensor_index']]['current'];
        # FIXME pass unit?
      }
      else
      {
        echo "no agent data!\n";
        continue;
      }
    } else if ($sensor['poller_type'] == "ipmi")
    {
      if (isset($ipmi_sensors))
      {
        $sensor_value = $ipmi_sensors[$class][$sensor['sensor_type']][$sensor['sensor_index']]['current'];
        $unit = $ipmi_sensors[$class][$sensor['sensor_type']][$sensor['sensor_index']]['unit'];
      }
      else
      {
        echo "no IPMI data!\n";
        continue;
      }
    }
    else
    {
      echo "unknown poller type!\n";
      continue;
    }

    if ($sensor_value == -32768) { echo("Invalid (-32768) "); $sensor_value = 0; }

    if ($sensor['sensor_divisor'])    { $sensor_value = $sensor_value / $sensor['sensor_divisor']; }
    if ($sensor['sensor_multiplier']) { $sensor_value = $sensor_value * $sensor['sensor_multiplier']; }

    $rrd_file = get_sensor_rrd($device, $sensor);

    if (!is_file($rrd_file))
    {
      rrdtool_create($rrd_file,"--step 300 \
      DS:sensor:GAUGE:600:-20000:U ".$config['rrd_rra']);
      //DS:sensor:GAUGE:600:-20000:20000 ".$config['rrd_rra']);
    }

    echo("$sensor_value $unit ");

    // FIXME also warn when crossing WARN level!!
    if ($sensor['sensor_ignore'] != "1")
     {
     if ($sensor['sensor_limit_low'] != "" && $sensor['sensor_value'] >= $sensor['sensor_limit_low'] && $sensor_value < $sensor['sensor_limit_low'])
     {
      $msg  = ucfirst($class) . " Alarm: " . $device['hostname'] . " " . $sensor['sensor_descr'] . " is under threshold: " . $sensor_value . "$unit (< " . $sensor['sensor_limit_low'] . "$unit)";
      notify($device, ucfirst($class) . " Alarm: " . $device['hostname'] . " " . $sensor['sensor_descr'], $msg);
      print_message("[%rAlerting for " . $device['hostname'] . " " . $sensor['sensor_descr'] . "%n\n", 'color');
      log_event(ucfirst($class) . ' ' . $sensor['sensor_descr'] . " under threshold: " . $sensor_value . " $unit (< " . $sensor['sensor_limit_low'] . " $unit)", $device, $class, $sensor['sensor_id']);
     }
     else if ($sensor['sensor_limit'] != "" && $sensor['sensor_value'] <= $sensor['sensor_limit'] && $sensor_value > $sensor['sensor_limit'])
     {
      $msg  = ucfirst($class) . " Alarm: " . $device['hostname'] . " " . $sensor['sensor_descr'] . " is over threshold: " . $sensor_value . "$unit (> " . $sensor['sensor_limit'] . "$unit)";
      notify($device, ucfirst($class) . " Alarm: " . $device['hostname'] . " " . $sensor['sensor_descr'], $msg);
      print_message("[%rAlerting for " . $device['hostname'] . " " . $sensor['sensor_descr'] . "%n\n", 'color');
      log_event(ucfirst($class) . ' ' . $sensor['sensor_descr'] . " above threshold: " . $sensor_value . " $unit (> " . $sensor['sensor_limit'] . " $unit)", $device, $class, $sensor['sensor_id']);
     }
    } else {
      print_message("[%ySensor Ignored%n]", 'color');
    }
    echo("\n");

    // Send statistics array via AMQP/JSON if AMQP is enabled globally and for the ports module
    if($config['amqp']['enable'] == TRUE && $config['amqp']['modules']['sensors'])
    {
      $json_data = array('value' => $sensor_value);
      messagebus_send(array('attribs' => array('t' => time(), 'device' => $device['hostname'], 'device_id' => $device['device_id'], 
                                               'e_type' => 'sensor', 'e_class' => $sensor['sensor_class'], 'e_type' => $sensor['sensor_type'], 'e_index' => $sensor['sensor_index']), 'data' => $json_data));
    }

    // Update StatsD/Carbon
    if($config['statsd']['enable'] == TRUE)
    {
      StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'sensor'.'.'.$sensor['sensor_class'].'.'.$sensor['sensor_type'].'.'.$sensor['sensor_index'], $sensor_value);
    }

    // Update RRD
    rrdtool_update($rrd_file,"N:$sensor_value");

    // Check alerts
    check_entity('sensor', $sensor, array('sensor_value' => $sensor_value));


    // Update SQL State
    if (is_numeric($sensor['sensor_polled']))
    {
      dbUpdate(array('sensor_value' => $sensor_value, 'sensor_polled' => time()), 'sensors-state', '`sensor_id` = ?', array($sensor['sensor_id']));
    } else {
      dbInsert(array('sensor_id' => $sensor['sensor_id'], 'sensor_value' => $sensor_value, 'sensor_polled' => time()), 'sensors-state');
    }

  }
}

function poll_device($device, $options)
{
  global $config, $debug, $device, $polled_devices, $db_stats, $memcache, $exec_status, $alert_rules, $alert_table;

  $oid_cache = array();

  $old_device_state = unserialize($device['device_state']);

  $attribs = get_dev_attribs($device['device_id']);

  $alert_rules = cache_alert_rules();
  $alert_table = cache_device_alert_table($device['device_id']);

  print_r($alert_rules);
  print_r($alert_table);

  $status = 0; unset($array);
  $device_start = utime();  // Start counting device poll time

  echo($device['hostname'] . " ".$device['device_id']." ".$device['os']." ");
  if ($config['os'][$device['os']]['group'])
  {
    $device['os_group'] = $config['os'][$device['os']]['group'];
    echo("(".$device['os_group'].")");
  }
  echo("\n");

  unset($poll_update); unset($poll_update_query); unset($poll_separator);
  $poll_update_array = array();

  $host_rrd = $config['rrd_dir'] . "/" . $device['hostname'];
  if (!is_dir($host_rrd)) { mkdir($host_rrd); echo("Created directory : $host_rrd\n"); }

  $device['pingable'] = isPingable($device['hostname']);
  if ($device['pingable'])
  {
    $device['snmpable'] = isSNMPable($device);
    if ($device['snmpable'])
    {
      $status = "1";
      $status_type = '';
    } else {
      echo("SNMP Unreachable");
      $status = "0";
      $status_type = ' (snmp)';
    }
  } else {
    echo("Unpingable");
    $status = "0";
    $status_type = ' (ping)';
  }

  if ($device['status'] != $status)
  {
    $poll_update .= $poll_separator . "`status` = '$status'";
    $poll_separator = ", ";

    dbUpdate(array('status' => $status), 'devices', 'device_id=?', array($device['device_id']));
    dbInsert(array('importance' => '0', 'device_id' => $device['device_id'], 'message' => "Device is " .($status == '1' ? 'up' : 'down')), 'alerts');

    log_event('Device status changed to ' . ($status == '1' ? 'Up' : 'Down') . $status_type, $device, 'system');
    notify($device, "Device ".($status == '1' ? 'Up' : 'Down').": " . $device['hostname'] . $status_type, "Device ".($status == '1' ? 'up' : 'down').": " . $device['hostname']);
  }

  $rrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/status.rrd";

  if (!is_file($rrd))
  {
    rrdtool_create ($rrd, "DS:status:GAUGE:600:0:1 ".$config['rrd_rra']);
  }

  if ($status == "1" || $status == "0")
  {
    rrdtool_update($rrd,"N:".$status);
  } else {
    rrdtool_update($rrd,"N:U");
  }

  // Ping response RRD database.
  $ping_rrd  = $config['rrd_dir'] . '/' . $device['hostname'] . '/ping.rrd';
  if (!is_file($ping_rrd))
  {
    rrdtool_create ($ping_rrd, "DS:ping:GAUGE:600:0:65535 " . $config['rrd_rra']);
  }
  if ($device['pingable'])
  {
    rrdtool_update($ping_rrd,"N:".$device['pingable']);
  } else {
    rrdtool_update($ping_rrd,"N:U");
  }

  // SNMP response RRD database.
  $ping_snmp_rrd  = $config['rrd_dir'] . '/' . $device['hostname'] . '/ping_snmp.rrd';
  if (!is_file($ping_snmp_rrd))
  {
    rrdtool_create ($ping_snmp_rrd, "DS:ping_snmp:GAUGE:600:0:65535 " . $config['rrd_rra']);
  }
  if ($device['snmpable'])
  {
    rrdtool_update($ping_snmp_rrd,"N:".$device['snmpable']);
  } else {
    rrdtool_update($ping_snmp_rrd,"N:U");
  }

  if ($status == "1")
  {
    $graphs = array();
    $oldgraphs = array();

    // Enable Ping graphs
    $graphs['ping'] = TRUE;

    // Enable SNMP graphs
    $graphs['ping_snmp'] = TRUE;

    if ($options['m'])
    {
      foreach (explode(",", $options['m']) as $module)
      {
        if (is_file("includes/polling/".$module.".inc.php"))
        {
          include("includes/polling/".$module.".inc.php");
        }
      }
    } else {
      foreach ($config['poller_modules'] as $module => $module_status)
      {
        if ($attribs['poll_'.$module] || ( $module_status && !isset($attribs['poll_'.$module])))
        {
          if ($debug) { echo("including: includes/polling/$module.inc.php\n"); }

          $m_start = utime();
          include('includes/polling/'.$module.'.inc.php');
          $m_end   = utime();
          $m_run   = round($m_end - $m_start, 4);
          $device_state['poller_mod_perf'][$module] = $m_run;
          echo("Module time: ".$m_run."s");

        } elseif (isset($attribs['poll_'.$module]) && $attribs['poll_'.$module] == "0") {
          echo("Module [ $module ] disabled on host.\n");
        } else {
          echo("Module [ $module ] disabled globally.\n");
        }
      }
    }


    if (!isset($options['m']))
    {
      // FIXME EVENTLOGGING -- MAKE IT SO WE DO THIS PER-MODULE?
      // This code cycles through the graphs already known in the database and the ones we've defined as being polled here
      // If there any don't match, they're added/deleted from the database.
      // Ideally we should hold graphs for xx days/weeks/polls so that we don't needlessly hide information.

      // Hardcoded poller performance
      $graphs['poller_perf'] = TRUE;

      foreach (dbFetch("SELECT `graph` FROM `device_graphs` WHERE `device_id` = ?", array($device['device_id'])) as $graph)
      {
        if (!isset($graphs[$graph["graph"]]))
        {
          dbDelete('device_graphs', "`device_id` = ? AND `graph` = ?", array($device['device_id'], $graph["graph"]));
        } else {
          $oldgraphs[$graph["graph"]] = TRUE;
        }
      }

      foreach ($graphs as $graph => $value)
      {
        if (!isset($oldgraphs[$graph]))
        {
          echo("+");
          dbInsert(array('device_id' => $device['device_id'], 'graph' => $graph), 'device_graphs');
        }
        echo($graph." ");
      }
    }

    $device_end = utime(); $device_run = $device_end - $device_start; $device_time = round($device_run, 4);

    $update_array['last_polled'] = array('NOW()');
    $update_array['last_polled_timetaken'] = $device_time;

    $update_array['device_state'] = serialize($device_state);

    #echo("$device_end - $device_start; $device_time $device_run");
    echo("Polled in $device_time seconds\n");

    // Only store performance data if we're not doing a single-module poll
    if (!$options['m'])
    {
      dbInsert(array('device_id' => $device['device_id'], 'operation' => 'poll', 'start' => $device_start, 'duration' => $device_run), 'devices_perftimes');

      $poller_rrd = $config['rrd_dir'] . "/" . $device['hostname'] . "/perf-poller.rrd";
      if (!is_file($poller_rrd))
      {
        rrdtool_create ($poller_rrd, "DS:val:GAUGE:600:0:38400 ".$config['rrd_rra']);
      }
      rrdtool_update($poller_rrd, "N:".$device_time);
    }

    if ($debug) { echo("Updating " . $device['hostname'] . " - ".print_r($update_array)." \n"); }

    $updated = dbUpdate($update_array, 'devices', '`device_id` = ?', array($device['device_id']));
    if ($updated) { echo("UPDATED!\n"); }

    unset($storage_cache); // Clear cache of hrStorage ** MAYBE FIXME? **
    unset($cache); // Clear cache (unify all things here?)
  }
}

?>
