<?php

// Common Functions

// Fix this shit, it's pretty uglytarded.
function hexStringToIPv4($string)
{
  $string = str_replace('"', '', $string);
  $string = trim($string);
  list($a,$b,$c,$d) = explode(" ", $string);
  $ip = hexdec($a).".".hexdec($b).".".hexdec($c).".".hexdec($d);

  return $ip;
}

# If a device is up, return its uptime, otherwise return the
# time since the last time we were able to poll it.  This
# is not very accurate, but better than reporting what the
# uptime was at some time before it went down.
function deviceUptime($device, $format="long")
{
  if ($device['status'] == 0) {
    $since = time() - strtotime( $device['last_polled'] );
    return "Down " . formatUptime( $since, $format );
  } else {
    return formatUptime($device['uptime'], $format);
  }
}

function formatUptime($diff, $format="long")
{
  $yearsDiff = floor($diff/31536000);
  $diff -= $yearsDiff*31536000;
  $daysDiff = floor($diff/86400);
  $diff -= $daysDiff*86400;
  $hrsDiff = floor($diff/60/60);
  $diff -= $hrsDiff*60*60;
  $minsDiff = floor($diff/60);
  $diff -= $minsDiff*60;
  $secsDiff = $diff;

  $uptime = "";

  if ($format == "short-2" || format == "shorter")
  {
    if ($yearsDiff > '0') { $u['y'] = $yearsDiff; }
    if ($daysDiff > '0')  { $u['d'] = $daysDiff; }
    if ($hrsDiff > '0')   { $u['h'] = $hrsDiff; }
    if ($minsDiff > '0')  { $u['m'] = $minsDiff; }
    if ($secsDiff > '0')  { $u['s'] = $secsDiff; }

    $count = 0; $uptime = '';
    foreach ($u as $period => $value)
    {
      if ($count == "2") { break; }
      $uptime .= $value.$period.' ';
      $count++;
    }
  }
  elseif ($format == "short-3")
  {
    if ($yearsDiff > '0') { $u['y'] = $yearsDiff; }
    if ($daysDiff > '0')  { $u['d'] = $daysDiff; }
    if ($hrsDiff > '0')   { $u['h'] = $hrsDiff; }
    if ($minsDiff > '0')  { $u['m'] = $minsDiff; }
    if ($secsDiff > '0')  { $u['s'] = $secsDiff; }

    $count = 0; $uptime = '';
    foreach ($u as $period => $value)
    {
      if ($count == "3") { break; }
      $uptime .= $value.$period.' ';
      $count++;
    }
  }
  elseif ($format == "short")
  {
    if ($yearsDiff > '0') { $uptime .= $yearsDiff . "y "; }
    if ($daysDiff > '0') { $uptime .= $daysDiff . "d "; }
    if ($hrsDiff > '0') { $uptime .= $hrsDiff . "h "; }
    if ($minsDiff > '0') { $uptime .= $minsDiff . "m "; }
    if ($secsDiff > '0') { $uptime .= $secsDiff . "s "; }
  }
  else
  {
    if ($yearsDiff > '0') { $uptime .= $yearsDiff . " years, "; }
    if ($daysDiff > '0') { $uptime .= $daysDiff . " day" . ($daysDiff != 1 ? 's' : '') . ", "; }
    if ($hrsDiff > '0') { $uptime .= $hrsDiff     . "h "; }
    if ($minsDiff > '0') { $uptime .= $minsDiff   . "m "; }
    if ($secsDiff > '0') { $uptime .= $secsDiff   . "s "; }
  }
  return trim($uptime);
}

function humanspeed($speed)
{
  $speed = formatRates($speed);
  if ($speed == "") { $speed = "-"; }
  return $speed;
}

function formatCiscoHardware(&$device, $short = false)
{
  if ($device['os'] == "ios")
  {
    if ($device['hardware'])
    {
      if (preg_match("/^WS-C([A-Za-z0-9]+).*/", $device['hardware'], $matches))
      {
        if (!$short)
        {
           $device['hardware'] = "Cisco " . $matches[1] . " (" . $device['hardware'] . ")";
        }
        else
        {
           $device['hardware'] = "Cisco " . $matches[1];
        }
      }
      elseif (preg_match("/^CISCO([0-9]+)$/", $device['hardware'], $matches))
      {
        $device['hardware'] = "Cisco " . $matches[1];
      }
    }
    else
    {
      if (preg_match("/Cisco IOS Software, C([A-Za-z0-9]+) Software.*/", $device['sysDescr'], $matches))
      {
        $device['hardware'] = "Cisco " . $matches[1];
      }
      elseif (preg_match("/Cisco IOS Software, ([0-9]+) Software.*/", $device['sysDescr'], $matches))
      {
        $device['hardware'] = "Cisco " . $matches[1];
      }
    }
  }
}

function format_number_short($number, $sf)
{
  // This formats a number so that we only send back three digits plus an optional decimal point.
  // Example: 723.42 -> 723    72.34 -> 72.3    2.23 -> 2.23

  list($whole, $decimal) = explode (".", $number);

  if (strlen($whole) >= $sf || !is_numeric($decimal))
  {
    $number = $whole;
  } elseif(strlen($whole) < $sf) {
    $diff = $sf - strlen($whole);
    $number = $whole .".".substr($decimal, 0, $diff);
  }
  return $number;
}

function external_exec($command)
{
  global $debug, $exec_status;

  //$command = str_replace(' 2>/dev/null', '', $command);
  $exec_status = array('command' => $command);

  if ($debug) { echo($command."\n"); }
  //$output = shell_exec($command); // old way

  $descriptorspec = array(
    //0 => array('pipe', 'r'), // stdin
    1 => array('pipe', 'w'), // stdout
    2 => array('pipe', 'w')  // stderr
  );
  $process = proc_open($command, $descriptorspec, $pipes);
  stream_set_blocking($pipes[2], 0); // Set nonblocking STDERR (very, very speeds up executing)
  if (is_resource($process))
  {
    $exec_status['error'] = stream_get_contents($pipes[2]);
    if ($exec_status['error'])
    {
      $output = FALSE;
    } else {
      $output = stream_get_contents($pipes[1]);
    }
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exec_status['status'] = proc_close($process);
  } else {
    $output = FALSE;
    $exec_error['error'] = '';
    $exec_status['status'] = -1;
  }
  if ($debug) { echo($output."\n"); }

  return $output;
}

function shorthost($hostname, $len = 0)
{
  global $config;

  if (!$len) { $len = $config['shorthost']['length']; }

  $parts = explode(".", $hostname);
  $shorthost = $parts[0];
  $i = 1;
  while ($i < count($parts) && strlen($shorthost.'.'.$parts[$i]) < $len)
  {
    $shorthost = $shorthost.'.'.$parts[$i];
    $i++;
  }
  return ($shorthost);
}

function isCli()
{
  if (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']))
  {
    return true;
  } else {
    return false;
  }
}

function print_error($text)
{
  print_message($text, 'error');
}

function print_warning($text)
{
  print_message($text, 'warning');
}

function print_message($text, $type='')
{
  global $config;
  $type = trim(strtolower($type));
  switch ($type)
  {
    case 'warning':
      $color = array('cli'       => '%b',                 // blue
                     'cli_color' => FALSE,                // by default cli coloring disabled
                     'class'     => 'alert');             // yellow
      $icon  = 'oicon-bell';
      break;
    case 'error':
      $color = array('cli'       => '%r',                 // red
                     'cli_color' => FALSE,                // by default cli coloring disabled
                     'class'     => 'alert alert-error'); // red
      $icon  = 'oicon-exclamation-red';
      break;
    case 'color':
      $color = array('cli'       => '',                  // none
                     'cli_color' => TRUE,                // allow using coloring
                     'class'     => 'alert alert-info'); // blue
      $icon  = 'oicon-information';
      break;
    default:
      $color = array('cli'       => '%W',                // bold
                     'cli_color' => FALSE,               // by default cli coloring disabled
                     'class'     => 'alert alert-info'); // blue
      $icon  = 'oicon-information';
      break;
  }
  
  if (isCli())
  {
    include_once($config['install_dir'] . "/includes/pear/Console/Color2.php");
    
    $msg = new Console_Color2();
    print $msg->convert($color['cli'].$text."%n\n", $color['cli_color']);
  } else {
  echo('
    <div class="'.$color['class'].'">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <i class="'.$icon.'"></i>
      '.$text.'
    </div>');
  }
}

function delete_port($int_id)
{
  global $config;

  $interface = dbFetchRow("SELECT * FROM `ports` AS P, `devices` AS D WHERE P.port_id = ? AND D.device_id = P.device_id", array($int_id));

  $interface_tables = array('adjacencies', 'ipaddr', 'ip6adjacencies', 'ip6addr', 'mac_accounting', 'bill_ports', 'pseudowires', 'ports');

  foreach ($interface_tables as $table)
  {
    dbDelete($table, "`port_id` =  ?", array($int_id));
  }

  dbDelete('links', "`local_port_id` =  ?", array($int_id));
  dbDelete('links', "`remote_port_id` =  ?", array($int_id));
  dbDelete('bill_ports', "`port_id` =  ?", array($int_id));

  $rrdfile = get_port_rrdfilename($interface, $interface);
  unlink($rrdfile);
}

function sgn($int)
{
  if ($int < 0)
  {
    return -1;
  } elseif ($int == 0) {
    return 0;
  } else {
    return 1;
  }
}

function get_sensor_rrd($device, $sensor)
{
  global $config;

  # For IPMI, sensors tend to change order, and there is no index, so we prefer to use the description as key here.
  if ($config['os'][$device['os']]['sensor_descr'] || $sensor['poller_type'] == "ipmi")
  {
    $rrd_file = $config['rrd_dir']."/".$device['hostname']."/".safename("sensor-".$sensor['sensor_class']."-".$sensor['sensor_type']."-".$sensor['sensor_descr'] . ".rrd");
  } else {
    $rrd_file = $config['rrd_dir']."/".$device['hostname']."/".safename("sensor-".$sensor['sensor_class']."-".$sensor['sensor_type']."-".$sensor['sensor_index'] . ".rrd");
  }

  return($rrd_file);
}

function get_port_by_index_cache($device_id, $ifIndex)
{
  global $port_index_cache;

  if (isset($port_index_cache[$device_id][$ifIndex]) && is_array($port_index_cache[$device_id][$ifIndex]))
  {
    $port = $port_index_cache[$device_id][$ifIndex];
  } else {
    $port = get_port_by_ifIndex($device_id, $ifIndex);
    humanize_port($port);
    $port_index_cache[$device_id][$ifIndex] = $port;
  }

  return $port;
}

function get_port_id_by_ifDescr($device_id, $ifDescr)
{
  $port = dbFetchRow("SELECT `port_id` FROM `ports` WHERE `device_id` = ? AND `ifDescr` = ? LIMIT 1", array($device_id, $ifDescr));

  if (is_array($port))
  {
    return $port['port_id'];
  } else {
    return FALSE;
  }
}

function get_port_by_ifIndex($device_id, $ifIndex)
{
  $port = dbFetchRow("SELECT * FROM `ports` WHERE `device_id` = ? AND `ifIndex` = ?", array($device_id, $ifIndex));
  humanize_port($port);
  if (is_array($port))
  {
    return $port;
  } else {
    return FALSE;
  }
}

function get_all_devices($device, $type = "")
{
  global $cache;

  // FIXME needs access control checks!
  // FIXME respect $type (server, network, etc) -- needs an array fill in topnav.

  if (isset($cache['devices']['hostname']))
  {
    $devices = array_keys($cache['devices']['hostname']);
  }
  else
  {
    foreach (dbFetchRows("SELECT `hostname` FROM `devices`") as $data)
    {
      $devices[] = $data['hostname'];
    }
  }

  return $devices;
}

function port_by_id_cache($port_id)
{
  return get_port_by_id_cache('port', $port_id);
}

function table_from_entity_type($type)
{
  // Fuck you, english pluralisation.
  if ($type == "storage")
  {
    return $type;
  } else {
    return $type."s";
  }
}

function get_entity_by_id_cache($type, $id)
{
  global $entity_cache;

  $table = table_from_entity_type($type);

  if (is_array($entity_cache[$type][$id]))
  {
    $entity = $entity_cache[$type][$id];
  } else {
    $entity = dbFetchRow("SELECT * FROM `".$table."` WHERE `".$type."_id` = ?", array($id));
    $entity_cache[$type][$id] = $entity;
  }
  return $entity;
}

function get_port_by_id($port_id)
{
  if (is_numeric($port_id))
  {
    $port = dbFetchRow("SELECT * FROM `ports` LEFT JOIN `ports-state` ON `ports`.`port_id` = `ports-state`.`port_id`  WHERE `ports`.`port_id` = ?", array($port_id));
  }
  if (is_array($port))
  {
    humanize_port($port);
    return $port;
  } else {
    return FALSE;
  }
}

function getImage($device)
{
  global $config;

  $device['os'] = strtolower($device['os']);

  if ($device['icon'] && file_exists($config['html_dir'] . "/images/os/" . $device['icon'] . ".png"))
  {
    $image = '<img src="' . $config['base_url'] . '/images/os/' . $device['icon'] . '.png" />';
  }
  elseif ($config['os'][$device['os']]['icon'] && file_exists($config['html_dir'] . "/images/os/" . $config['os'][$device['os']]['icon'] . ".png"))
  {
    $image = '<img src="' . $config['base_url'] . '/images/os/' . $config['os'][$device['os']]['icon'] . '.png" />';
  } else {
    if (file_exists($config['html_dir'] . '/images/os/' . $device['os'] . '.png'))
    {
      $image = '<img src="' . $config['base_url'] . '/images/os/' . $device['os'] . '.png" />';
    }
    if ($device['os'] == "linux")
    {
      $features = strtolower(trim($device['features']));
      list($distro) = explode(" ", $features);
      if (file_exists($config['html_dir'] . "/images/os/$distro" . ".png"))
      {
        $image = '<img src="' . $config['base_url'] . '/images/os/' . $distro . '.png" />';
      }
    }
  }

  return $image;
}

function get_application_by_id($application_id)
{
  if (is_numeric($application_id))
  {
    $application = dbFetchRow("SELECT * FROM `applications` WHERE `app_id` = ?", array($application_id));
  }
  if (is_array($application))
  {
    return $application;
  } else {
    return FALSE;
  }
}

function get_sensor_by_id($sensor_id)
{
  if (is_numeric($sensor_id))
  {
    $sensor = dbFetchRow("SELECT * FROM `sensors` WHERE `sensor_id` = ?", array($sensor_id));
  }
  if (is_array($sensor))
  {
    return $sensor;
  } else {
    return FALSE;
  }
}

function get_device_id_by_port_id($port_id)
{
  if (is_numeric($port_id))
  {
    $device_id = dbFetchCell("SELECT `device_id` FROM `ports` WHERE `port_id` = ?", array($port_id));
  }
  if (is_numeric($device_id))
  {
    return $device_id;
  } else {
    return FALSE;
  }
}

function get_device_id_by_app_id($app_id)
{
  if (is_numeric($app_id))
  {
    $device_id = dbFetchCell("SELECT `device_id` FROM `applications` WHERE `app_id` = ?", array($app_id));
  }
  if (is_numeric($device_id))
  {
    return $device_id;
  } else {
    return FALSE;
  }
}

function ifclass($ifOperStatus, $ifAdminStatus)
{
  $ifclass = "interface-upup";
  if ($ifAdminStatus == "down") { $ifclass = "gray"; }
  if ($ifAdminStatus == "up" && $ifOperStatus== "down") { $ifclass = "red"; }
  if ($ifAdminStatus == "up" && $ifOperStatus== "lowerLayerDown") { $ifclass = "orange"; }
  if ($ifAdminStatus == "up" && $ifOperStatus== "up") { $ifclass = ""; }

  return $ifclass;
}

function device_by_name($name, $refresh = 0)
{
  // FIXME - cache name > id too.
  return device_by_id_cache(getidbyname($name), $refresh);
}

function accesspoint_by_id($ap_id, $refresh = '0') {

  $ap = dbFetchRow("SELECT * FROM `accesspoints` WHERE `accesspoint_id` = ?", array($ap_id));

  return $ap;

}

function device_by_id_cache($device_id, $refresh = '0')
{
  global $cache;

  if (!$refresh && isset($cache['devices']['id'][$device_id]) && is_array($cache['devices']['id'][$device_id]))
  {
    $device = $cache['devices']['id'][$device_id];
  } else {
    $device = dbFetchRow("SELECT * FROM `devices` WHERE `device_id` = ?", array($device_id));
    humanize_device($device);
    $cache['devices']['id'][$device_id] = $device;
  }
  return $device;
}

function truncate($substring, $max = 50, $rep = '...')
{
  if (strlen($substring) < 1) { $string = $rep; } else { $string = $substring; }
  $leave = $max - strlen ($rep);
  if (strlen($string) > $max) { return substr_replace($string, $rep, $leave); } else { return $string; }
}

function mres($string)
{ // short function wrapper because the real one is stupidly long and ugly. aesthetics.
  return mysql_real_escape_string($string);
}

function getifhost($id)
{
  return dbFetchCell("SELECT `device_id` from `ports` WHERE `port_id` = ?", array($id));
}

function gethostbyid($id)
{
  global $cache;

  if (isset($cache['devices']['id'][$id]['hostname']))
  {
    $hostname = $cache['devices']['id'][$id]['hostname'];
  }
  else
  {
    $hostname = dbFetchCell("SELECT `hostname` FROM `devices` WHERE `device_id` = ?", array($id));
  }

  return $hostname;
}

function strgen ($length = 16)
{
  $entropy = array(0,1,2,3,4,5,6,7,8,9,'a','A','b','B','c','C','d','D','e',
  'E','f','F','g','G','h','H','i','I','j','J','k','K','l','L','m','M','n',
  'N','o','O','p','P','q','Q','r','R','s','S','t','T','u','U','v','V','w',
  'W','x','X','y','Y','z','Z');
  $string = "";

  for ($i=0; $i<$length; $i++)
  {
    $key = mt_rand(0,61);
    $string .= $entropy[$key];
  }

  return $string;
}

function getpeerhost($id)
{
  return dbFetchCell("SELECT `device_id` from `bgpPeers` WHERE `bgpPeer_id` = ?", array($id));
}

function getifindexbyid($id)
{
  return dbFetchCell("SELECT `ifIndex` FROM `ports` WHERE `port_id` = ?", array($id));
}

function getifbyid($id)
{
  return dbFetchRow("SELECT * FROM `ports` WHERE `port_id` = ?", array($id));
}

function getifdescrbyid($id)
{
  return dbFetchCell("SELECT `ifDescr` FROM `ports` WHERE `port_id` = ?", array($id));
}

function getidbyname($hostname)
{
  global $cache;

  if (isset($cache['devices']['hostname'][$hostname]))
  {
    $id = $cache['devices']['hostname'][$hostname];
  } else
  {
    $id = dbFetchCell("SELECT `device_id` FROM `devices` WHERE `hostname` = ?", array($hostname));
  }

  return $id;
}

function gethostosbyid($id)
{
  global $cache;

  if (isset($cache['devices']['id'][$id]['os']))
  {
    $os = $cache['devices']['id'][$id]['os'];
  }
  else
  {
    $os = dbFetchCell("SELECT `os` FROM `devices` WHERE `device_id` = ?", array($id));
  }

  return $os;
}

function safename($name)
{
  return preg_replace('/[^a-zA-Z0-9,._\-]/', '_', $name);
}

function zeropad($num, $length = 2)
{
  while (strlen($num) < $length)
  {
    $num = '0'.$num;
  }

  return $num;
}

function set_dev_attrib($device, $attrib_type, $attrib_value)
{
  if (dbFetchCell("SELECT COUNT(*) FROM devices_attribs WHERE `device_id` = ? AND `attrib_type` = ?", array($device['device_id'],$attrib_type)))
  {
    $return = dbUpdate(array('attrib_value' => $attrib_value), 'devices_attribs', 'device_id=? and attrib_type=?', array($device['device_id'], $attrib_type));
  }
  else
  {
    $return = dbInsert(array('device_id' => $device['device_id'], 'attrib_type' => $attrib_type, 'attrib_value' => $attrib_value), 'devices_attribs');
  }
  return $return;
}

function get_dev_attribs($device_id)
{
  $attribs = array();
  foreach (dbFetchRows("SELECT * FROM devices_attribs WHERE `device_id` = ?", array($device_id)) as $entry)
  {
    $attribs[$entry['attrib_type']] = $entry['attrib_value'];
  }
  return $attribs;
}

function get_dev_entity_state($device)
{
  $state = array();
  foreach (dbFetchRows("SELECT * FROM entPhysical_state WHERE `device_id` = ?", array($device)) as $entity)
  {
    $state['group'][$entity['group']][$entity['entPhysicalIndex']][$entity['subindex']][$entity['key']] = $entity['value'];
    $state['index'][$entity['entPhysicalIndex']][$entity['subindex']][$entity['group']][$entity['key']] = $entity['value'];
  }
  return $state;
}

function get_dev_attrib($device, $attrib_type)
{
  if ($row = dbFetchRow("SELECT attrib_value FROM devices_attribs WHERE `device_id` = ? AND `attrib_type` = ?", array($device['device_id'], $attrib_type)))
  {
    return $row['attrib_value'];
  }
  else
  {
    return NULL;
  }
}

function del_dev_attrib($device, $attrib_type)
{
  return dbDelete('devices_attribs', "`device_id` = ? AND `attrib_type` = ?", array($device['device_id'], $attrib_type));
}

function formatRates($value, $round = '2', $sf = '3')
{
   $value = format_si($value, $round, $sf) . "bps";
   return $value;
}

function formatStorage($value, $round = '2', $sf = '3')
{
   $value = format_bi($value, $round) . "B";
   return $value;
}

function format_si($value, $round = '2', $sf = '3')
{
  if ($value < "0")
  {
    $neg = 1;
    $value = $value * -1;
  }

  if ($value >= "0.1")
  {
    $sizes = Array('', 'k', 'M', 'G', 'T', 'P', 'E');
    $ext = $sizes[0];
    for ($i = 1; (($i < count($sizes)) && ($value >= 1000)); $i++) { $value = $value / 1000; $ext  = $sizes[$i]; }
  }
  else
  {
    $sizes = Array('', 'm', 'u', 'n');
    $ext = $sizes[0];
    for ($i = 1; (($i < count($sizes)) && ($value != 0) && ($value <= 0.1)); $i++) { $value = $value * 1000; $ext  = $sizes[$i]; }
  }

  if ($neg) { $value = $value * -1; }

  return format_number_short(round($value, $round),$sf).$ext;
}

function format_bi($value, $round = '2', $sf = '3')
{
  if ($value < "0")
  {
    $neg = 1;
    $value = $value * -1;
  }
  $sizes = Array('', 'k', 'M', 'G', 'T', 'P', 'E');
  $ext = $sizes[0];
  for ($i = 1; (($i < count($sizes)) && ($value >= 1024)); $i++) { $value = $value / 1024; $ext  = $sizes[$i]; }

  if ($neg) { $value = $value * -1; }

  return format_number_short(round($value, $round), $sf).$ext;
}

function format_number($value, $base = '1000', $round=2, $sf=3)
{
  if ($base == '1000')
  {
    return format_si($value, $round, $sf);
  } else {
    return format_bi($value, $round, $sf);
  }
}

function is_valid_hostname($hostname)
{
  // The Internet standards (Request for Comments) for protocols mandate that
  // component hostname labels may contain only the ASCII letters 'a' through 'z'
  // (in a case-insensitive manner), the digits '0' through '9', and the hyphen
  // ('-'). The original specification of hostnames in RFC 952, mandated that
  // labels could not start with a digit or with a hyphen, and must not end with
  // a hyphen. However, a subsequent specification (RFC 1123) permitted hostname
  // labels to start with digits. No other symbols, punctuation characters, or
  // white space are permitted. While a hostname may not contain other characters,
  // such as the underscore character (_), other DNS names may contain the underscore

  return ctype_alnum(str_replace('_','',str_replace('-','',str_replace('.','',$hostname))));
}

// get $host record from /etc/hosts
function ipFromEtcHosts($host) {
  foreach (new SplFileObject('/etc/hosts') as $line) {
    $d = preg_split('/\s/', $line, -1, PREG_SPLIT_NO_EMPTY);
    if (empty($d) || substr(reset($d), 0, 1) == '#') continue;
    $ip = array_shift($d);
    $hosts = array_map('strtolower', $d);
    if (in_array(strtolower($host), $hosts)) return $ip;
  }
  return FALSE;
}

function gethostbyname6($host, $try_a = false) {
  // get AAAA record for $host
  // if $try_a is true, if AAAA fails, it tries for A
  // the first match found is returned
  // otherwise returns false

  $dns = gethostbynamel6($host, $try_a);
  if ($dns == false) {
    return false;
  } else {
    return $dns[0];
  }
}

function gethostbynamel6($host, $try_a = false) {
  // get AAAA records for $host,
  // if $try_a is true, if AAAA fails, it tries for A
  // results are returned in an array of ips found matching type
  // otherwise returns false

  $ip6 = array();
  $ip4 = array();

  // First try /etc/hosts
  /// FIXME. Mike: it is necessary to use nsswitch, but I yet didn't think up as.
  $etc = ipFromEtcHosts($host);
  if ($etc && strstr($etc, ':')) $ip6[] = $etc;

  if ($try_a == true) {
    if ($etc && strstr($etc, '.')) $ip4[] = $etc;
    $dns = dns_get_record($host, DNS_A + DNS_AAAA);
  } else {
    $dns = dns_get_record($host, DNS_AAAA);
  }

  foreach ($dns as $record) {
    switch ($record['type']) {
      case 'A':
        $ip4[] = $record['ip'];
        break;
      case 'AAAA':
        $ip6[] = $record['ipv6'];
        break;
    }
  }

  if (count($ip6) < 1) {
    if ($try_a == true) {
      if (count($ip4) < 1) {
        return false;
      } else {
        return $ip4;
      }
    } else {
      return false;
    }
  } else {
    return $ip6;
  }
}

function add_service($device, $service, $descr)
{
  $insert = array('device_id' => $device['device_id'], 'service_ip' => $device['hostname'], 'service_type' => $service,
                  'service_changed' => array('UNIX_TIMESTAMP(NOW())'), 'service_desc' => $descr, 'service_param' => "", 'service_ignore' => "0");

  echo dbInsert($insert, 'services');
}

function get_port_rrdfilename($device, $interface, $suffix = "")
{
  global $config;

  if ($device['hostname'] == "")
  {
    die(" Error: hostname for device is empty\n");
  }

  $device_identifier = strtolower($config['os'][$device['os']]['port_rrd_identifier']);

  // default to ifIndex
  $this_port_identifier = $interface['ifIndex'];

  if ($device_identifier == "ifname" && $interface['ifName'] != "")
  {
    $this_port_identifier = strtolower(str_replace("/", "-", $interface['ifName']));
  }

  if ($suffix == "")
  {
    return sprintf("%s/%s/port-%s.rrd", trim($config['rrd_dir']), trim($device['hostname']), $this_port_identifier);
  }
  else
  {
    return sprintf("%s/%s/port-%s-%s.rrd", trim($config['rrd_dir']), trim($device['hostname']), $this_port_identifier, $suffix);
  }
}

function get_http_request($request)
{
  global $config, $debug;
  $response = '';

  $opts = array('http' => array('timeout' => '20'));
  if(isset($config['http_proxy']) && $config['http_proxy'])
  {
    $opts['http']['proxy'] = 'tcp://' . $config['http_proxy'];
    $opts['http']['request_fulluri'] = TRUE;
  }

  $context = stream_context_create($opts);
  $response = file_get_contents($request, FALSE, $context);

  return $response;
}

?>
