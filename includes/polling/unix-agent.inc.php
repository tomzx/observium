<?php

/// FIXME. From this uses only check_valid_sensors(), maybe need move to global functions or copy to polling. --mike
include_once("includes/discovery/functions.inc.php");

global $debug, $valid, $agent_sensors;

if ($device['os_group'] == "unix")
{
  echo("Observium UNIX Agent: ");

  // FIXME - this should be overridable in database
  $agent_port = $config['unix-agent']['port'];

  // Try Official port
  $agent_start = utime();
  $agent_socket = "tcp://".$device['hostname'].":".$agent_port;
  $agent = @stream_socket_client($agent_socket, $errno, $errstr, 10);

  if(!$agent)
  {
    echo("Connection to UNIX agent on ".$agent_socket." failed. ERROR: ".$errno." ".$errstr."\n");
    logfile("Connection to UNIX agent on ".$agent_socket." failed. ERROR: ".$errno." ".$errstr);

    /// Try check_mk port if the official one doesn't work.
    $agent_port = "6556";
    $agent_start = utime();
    $agent_socket = "tcp://".$device['hostname'].":".$agent_port;
    $agent = @stream_socket_client($agent_socket, $errno, $errstr, 10);

    if (!$agent)
    {
      echo("Connection to UNIX agent on ".$agent_socket." failed. ERROR: ".$errno." ".$errstr."\n");
      logfile("Connection to UNIX agent on ".$agent_socket." failed. ERROR: ".$errno." ".$errstr);
    } else {
      $agent_raw = stream_get_contents($agent);
    }
  } else {
    $agent_raw = stream_get_contents($agent);
  }

  $agent_end = utime(); $agent_time = round(($agent_end - $agent_start) * 1000);

  if (!empty($agent_raw))
  {
    echo("execution time: ".$agent_time."ms");
    $agent_rrd = $config['rrd_dir'] . "/" . $device['hostname'] . "/agent.rrd";
    if (!is_file($agent_rrd))
    {
      rrdtool_create ($agent_rrd, "DS:time:GAUGE:600:0:U ".$config['rrd_rra']);
    }
    rrdtool_update($agent_rrd, "N:".$agent_time);
    $graphs['agent'] = TRUE;

    foreach (explode("<<<", $agent_raw) as $section)
    {
      list($section, $data) = explode(">>>", $section);
      list($sa, $sb, $sc) = explode("-", $section, 3);

      ## Compatibility with versions of scripts with and without app-
      ## Disabled for DRBD because it falsely detects the check_mk output

      if ($section == "apache") { $sa = "app"; $sb = "apache"; }
      if ($section == "mysql")  { $sa = "app"; $sb = "mysql"; }
      if ($section == "nginx")  { $sa = "app"; $sb = "nginx"; }

      # FIXME why is this here? New application scripts should just return app-$foo
      if ($section == "freeradius")  { $sa = "app"; $sb = "freeradius"; }
      if ($section == "postfix_qshape")  { $sa = "app"; $sb = "postfix_qshape"; }
      if ($section == "postfix_mailgraph")  { $sa = "app"; $sb = "postfix_mailgraph"; }
#      if ($section == "drbd")   { $sa = "app"; $sb = "drbd"; }
      
      # Workaround for older script where we didn't split into 3 possible parts yet
      if ($section == "app-powerdns-recursor") { $sa = "app"; $sb = "powerdns-recursor"; $sc = ""; }
      if ($section == "app-exim-mailqueue") { $sa = "app"; $sb = "exim-mailqueue"; $sc = ""; }

      if (!empty($sa) && !empty($sb))
      {
        if (!empty($sc))
        {
          $agent_data[$sa][$sb][$sc] = trim($data);
        } else {
          $agent_data[$sa][$sb] = trim($data);
        }
      } else {
        $agent_data[$section] = trim($data);
      }
    }
    
    $agent_sensors = array(); # Init to empty to be able to use array_merge() later on

    if ($debug) { print_r($agent_data); }

    include("unix-agent/packages.inc.php");
    include("unix-agent/munin-plugins.inc.php");

    foreach (array_keys($agent_data) as $key)
    {
      if (file_exists("includes/polling/unix-agent/$key.inc.php"))
      {
        if ($debug) { echo("Including: unix-agent/$key.inc.php"); }

        include("unix-agent/$key.inc.php");
      }
    }

    if (is_array($agent_data['app']))
    {
      foreach (array_keys($agent_data['app']) as $key)
      {
        if (file_exists("includes/polling/applications/$key.inc.php"))
        {
          echo(" ");
          $app = @dbFetchRow("SELECT * FROM `applications` WHERE `device_id` = ? AND `app_type` = ?", array($device['device_id'],$key));

          if (empty($app))
          {
            @dbInsert(array('device_id' => $device['device_id'], 'app_type' => $key, 'app_state' => 'UNKNOWN'), 'applications');
            echo("+");
          }

          if ($debug) { echo("Including: applications/$key.inc.php"); }

          echo($key);

          include("includes/polling/applications/$key.inc.php");
        }
      }
    }

    // Processes
    if (!empty($agent_data['ps']))
    {
      echo("\nProcesses: ");
      foreach (explode("\n", $agent_data['ps']) as $process)
      {
        $process = preg_replace("/\((.*),([0-9]*),([0-9]*),([0-9\.]*)\)\ (.*)/", "\\1|\\2|\\3|\\4|\\5", $process);
        list($user, $vsz, $rss, $pcpu, $command) = explode("|", $process, 5);
          $processlist[] = array('user' => $user, 'vsz' => $vsz, 'rss' => $rss, 'pcpu' => $pcpu, 'command' => $command);
      }
      #print_r($processlist);
      echo("\n");
    }

    // Apache
    if (!empty($agent_data['app']['apache']))
    {
      $app_found['apache'] = TRUE;
      if (dbFetchCell("SELECT COUNT(*) FROM `applications` WHERE `device_id` = ? AND `app_type` = ?", array($device['device_id'], 'apache')) == "0")
      {
        echo("Found new application 'Apache'\n");
        dbInsert(array('device_id' => $device['device_id'], 'app_type' => 'apache'), 'applications');
      }
    }

    // Memcached
    if (!empty($agent_data['app']['memcached']))
    {
      foreach ($agent_data['app']['memcached'] as $memcached_host => $memcached_data)
{
        if (dbFetchCell("SELECT COUNT(*) FROM `applications` WHERE `device_id` = ? AND `app_type` = ? AND `app_instance` = ?", array($device['device_id'], 'memcached', $memcached_host)) == "0")
        {
          echo("Found new application 'Memcached $instance'\n");
          dbInsert(array('device_id' => $device['device_id'], 'app_type' => 'memcached', 'app_instance' => $memcached_host), 'applications');
        }
      }
    }

    // MySQL
    if (!empty($agent_data['app']['mysql']))
    {
      $app_found['mysql'] = TRUE;
      if (dbFetchCell("SELECT COUNT(*) FROM `applications` WHERE `device_id` = ? AND `app_type` = ?", array($device['device_id'], 'mysql')) == "0")
      {
        echo("Found new application 'MySQL'\n");
        dbInsert(array('device_id' => $device['device_id'], 'app_type' => 'mysql'), 'applications');
      }
    }

    // DRBD
    if (!empty($agent_data['drbd']))
    {
      $agent_data['app']['drbd'] = array();
      foreach (explode("\n", $agent_data['drbd']) as $drbd_entry)
      {
        list($drbd_dev, $drbd_data) = explode(":", $drbd_entry);
        if (preg_match("/^drbd/", $drbd_dev))
        {
          $agent_data['app']['drbd'][$drbd_dev] = $drbd_data;
          if (dbFetchCell("SELECT COUNT(*) FROM `applications` WHERE `device_id` = ? AND `app_type` = ? AND `app_instance` = ?", array($device['device_id'], 'drbd', $drbd_dev)) == "0")
          {
            echo("Found new application 'DRBD' $drbd_dev\n");
            dbInsert(array('device_id' => $device['device_id'], 'app_type' => 'drbd', 'app_instance' => $drbd_dev), 'applications');
          }
        }
      }
    }
  }

  echo("Sensors: ");
  foreach (array_keys($config['sensor_classes']) as $sensor_class)
  {
    check_valid_sensors($device, $sensor_class, $valid['sensor'], 'agent');
  }
  echo("\n");
}

?>
