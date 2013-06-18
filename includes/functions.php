<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage functions
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2012 Adam Armstrong
 *
 */

// Observium Includes

include_once($config['install_dir'] . "/includes/common.php");
include_once($config['install_dir'] . "/includes/rrdtool.inc.php");
include_once($config['install_dir'] . "/includes/billing.php");
include_once($config['install_dir'] . "/includes/cisco-entities.php");
include_once($config['install_dir'] . "/includes/syslog.php");
include_once($config['install_dir'] . "/includes/rewrites.php");
include_once($config['install_dir'] . "/includes/snmp.inc.php");
include_once($config['install_dir'] . "/includes/services.inc.php");
include_once($config['install_dir'] . "/includes/dbFacile.php");
include_once($config['install_dir'] . "/includes/geolocation.inc.php");

// Include from PEAR
set_include_path($config['install_dir'] . "/includes/pear" . PATH_SEPARATOR . get_include_path());
include_once($config['install_dir'] . "/includes/pear/Net/IPv4.php");
include_once($config['install_dir'] . "/includes/pear/Net/IPv6.php");
include_once($config['install_dir'] . "/includes/pear/Net/MAC.php");

if ($config['alerts']['email']['enable'])
{
  // Use Pear::Mail
  include_once($config['install_dir'] . "/includes/pear/Mail/Mail.php");
}

function array_sort($array, $on, $order=SORT_ASC)
{
  $new_array = array();
  $sortable_array = array();

  if (count($array) > 0)
  {
    foreach ($array as $k => $v)
    {
      if (is_array($v))
      {
        foreach ($v as $k2 => $v2)
        {
          if ($k2 == $on)
          {
            $sortable_array[$k] = $v2;
          }
        }
      } else {
        $sortable_array[$k] = $v;
      }
    }

    switch ($order)
    {
      case SORT_ASC:
        asort($sortable_array);
      break;
      case SORT_DESC:
        arsort($sortable_array);
      break;
    }

    foreach ($sortable_array as $k => $v)
    {
      $new_array[$k] = $array[$k];
    }
  }

  return $new_array;
}

function include_wrapper($filename)
{
  global $config;
  include($filename);
}

function mac_clean_to_readable($mac)
{
  for ($i = 0; $i < 12; $i+=2) { $r[] .= substr($mac, $i, 2); }
  return implode($r, ':');
}

function only_alphanumeric($string)
{
  return preg_replace('/[^a-zA-Z0-9]/', '', $string);
}

function logfile($string)
{
  global $config;

  $fd = fopen($config['log_file'],'a');
  fputs($fd,$string . "\n");
  fclose($fd);
}

function error($message)
{
  global $config, $debug;

  if ($debug) { echo($message); }
}

function get_device_os($device)
{
  global $config, $debug;

  $sysDescr    = snmp_get ($device, "SNMPv2-MIB::sysDescr.0", "-Ovq");
  $sysObjectId = snmp_get ($device, "SNMPv2-MIB::sysObjectID.0", "-Ovqn");

  if ($debug)
  {
    echo("| $sysDescr | $sysObjectId | ");
  }

  $dir_handle = @opendir($config['install_dir'] . "/includes/discovery/os") or die("Unable to open $path");
  while ($file = readdir($dir_handle))
  {
    if (preg_match("/.php$/", $file))
    {
      include($config['install_dir'] . "/includes/discovery/os/" . $file);
    }
  }
  closedir($dir_handle);

  if ($os) { return $os; } else { return "generic"; }
}

function interface_errors($rrd_file, $period = '-1d') // Returns the last in/out errors value in RRD
{
  global $config;

  $cmd = $config['rrdtool']." fetch -s $period -e -300s $rrd_file AVERAGE | grep : | cut -d\" \" -f 4,5";
  $data = trim(shell_exec($cmd));
  foreach (explode("\n", $data) as $entry)
  {
    list($in, $out) = explode(" ", $entry);
    $in_errors += ($in * 300);
    $out_errors += ($out * 300);
  }
  $errors['in'] = round($in_errors);
  $errors['out'] = round($out_errors);

  return $errors;
}

function renamehost($id, $new, $source = 'console')
{
  global $config;

  // Test if new host exists in database
  if (dbFetchCell('SELECT COUNT(device_id) FROM `devices` WHERE `hostname` = ?', array($new)) == 0)
  {
    // Test DNS lookup.
    if (gethostbyname6($new, TRUE))
    {
      // Test reachability
      if (isPingable($new))
      {
        $host = dbFetchCell("SELECT `hostname` FROM `devices` WHERE `device_id` = ?", array($id));
        rename($config['rrd_dir']."/$host",$config['rrd_dir']."/$new");
        $return = dbUpdate(array('hostname' => $new), 'devices', 'device_id=?', array($id));
        log_event("Hostname changed -> $new ($source)", $id, 'system');
        return TRUE;
      } else {
        // failed Reachability
        print_error("Could not ping $new");
      }
    } else {
      // Failed DNS lookup
      print_error("Could not resolve $new");
    }
  } else {
    // found in database
    print_error("Already got host $new");
  }
  return FALSE;
}

function delete_device($id)
{
  global $config;

  $host = dbFetchCell("SELECT hostname FROM devices WHERE device_id = ?", array($id));

  foreach (dbFetch("SELECT * FROM `ports` WHERE `device_id` = ?", array($id)) as $int_data)
  {
    $int_if = $int_data['ifDescr'];
    $int_id = $int_data['port_id'];
    delete_port($int_id);
    $ret .= "Removed interface $int_id ($int_if)\n";
  }

  dbDelete('devices', "`device_id` =  ?", array($id));

  $device_tables = array('entPhysical', 'devices_attribs', 'devices_perms', 'bgpPeers', 'vlans', 'vrfs', 'storage', 'alerts', 'eventlog',
                         'syslog', 'ports', 'services', 'toner', 'frequency', 'current', 'sensors', 'ospf_areas', 'ospf_ports', 'ospf_nbrs', 'ospf_instances');

  foreach ($device_tables as $table)
  {
    dbDelete($table, "`device_id` =  ?", array($id));
  }

  #shell_exec("rm -rf ".trim($config['rrd_dir'])."/$host");

  $ret = "Removed device $host\n";
  return $ret;
}

function addHost($host, $snmpver, $port = '161', $transport = 'udp')
{
  global $config;

  list($hostshort) = explode(".", $host);
  // Test if host exists in database
  if (dbFetchCell("SELECT COUNT(*) FROM `devices` WHERE `hostname` = ?", array($host)) == '0')
  {
    // Test DNS lookup.
    if (gethostbyname6($host, TRUE))
    {
      // Test reachability
      if (isPingable($host))
      {
        $added = 0;

        if (empty($snmpver))
        {
          // Try SNMPv2c
          $snmpver = 'v2c';
          $ret = addHost($host, $snmpver, $port, $transport);
          if (!$ret)
          {
            //Try SNMPv3
            $snmpver = 'v3';
            $ret = addHost($host, $snmpver, $port, $transport);
            if (!$ret)
            {
              // Try SNMPv1
              $snmpver = 'v1';
              return addHost($host, $snmpver, $port, $transport);
            } else {
              return $ret;
            }
          } else {
            return $ret;
          }
        }

        if ($snmpver === "v3")
        {
          // Try each set of parameters from config
          foreach ($config['snmp']['v3'] as $v3)
          {
            $device = deviceArray($host, NULL, $snmpver, $port, $transport, $v3);
            print_message("Trying v3 parameters " . $v3['authname'] . "/" .  $v3['authlevel'] . " ... ");
            if (isSNMPable($device))
            {
              $snmphost = snmp_get($device, "sysName.0", "-Oqv", "SNMPv2-MIB");
              if (empty($snmphost) or ($snmphost == $host || $hostshort = $host))
              {
                $device_id = createHost($host, NULL, $snmpver, $port, $transport, $v3);
                return $device_id;
              } else {
                print_error("Given hostname does not match SNMP-read hostname ($snmphost)!");
              }
            } else {
              print_error("No reply on credentials " . $v3['authname'] . "/" .  $v3['authlevel'] . " using $snmpver");
            }
          }
        }
        elseif ($snmpver === "v2c" or $snmpver === "v1")
        {
          // Try each community from config
          foreach ($config['snmp']['community'] as $community)
          {
            $device = deviceArray($host, $community, $snmpver, $port, $transport, NULL);
            print_message("Trying community $community ...");
            if (isSNMPable($device))
            {
              $snmphost = snmp_get($device, "sysName.0", "-Oqv", "SNMPv2-MIB");
              if ($snmphost == "" || ($snmphost && ($snmphost == $host || $hostshort = $host)))
              {
                $device_id = createHost($host, $community, $snmpver, $port, $transport);
                return $device_id;
              } else {
                print_error("Given hostname does not match SNMP-read hostname ($snmphost)!");
              }
            } else {
              print_error("No reply on community $community using $snmpver");
            }
          }
        }
        else
        {
          print_error("Unsupported SNMP Version \"$snmpver\".");
        }

        if (!$device_id)
        {
          // Failed SNMP
          print_error("Could not reach $host with given SNMP community using $snmpver");
        }
      } else {
        // failed Reachability
        print_error("Could not ping $host");
      }
    } else {
      // Failed DNS lookup
      print_error("Could not resolve $host");
    }
  } else {
    // found in database
    print_error("Already got host $host");
  }

  return 0;
}

function scanUDP($host, $port, $timeout)
{
  $handle = fsockopen($host, $port, $errno, $errstr, 2);
  socket_set_timeout ($handle, $timeout);
  $write = fwrite($handle,"\x00");
  if (!$write) { next; }
  $startTime = time();
  $header = fread($handle, 1);
  $endTime = time();
  $timeDiff = $endTime - $startTime;

  if ($timeDiff >= $timeout)
  {
    fclose($handle); return 1;
  } else { fclose($handle); return 0; }
}

function deviceArray($host, $community, $snmpver, $port = 161, $transport = 'udp', $v3)
{
  $device = array();
  $device['hostname'] = $host;
  $device['port'] = $port;
  $device['transport'] = $transport;
  $device['snmpver'] = $snmpver;

  if ($snmpver === "v2c" or $snmpver === "v1")
  {
    $device['community'] = $community;
  }
  elseif ($snmpver === "v3")
  {
    $device['authlevel']  = $v3['authlevel'];
    $device['authname']   = $v3['authname'];
    $device['authpass']   = $v3['authpass'];
    $device['authalgo']   = $v3['authalgo'];
    $device['cryptopass'] = $v3['cryptopass'];
    $device['cryptoalgo'] = $v3['cryptoalgo'];
  }

  return $device;
}

function netmask2cidr($netmask)
{
  $addr = Net_IPv4::parseAddress("1.2.3.4/$netmask");
  return $addr->bitmask;
}

function cidr2netmask()
{
  return (long2ip(ip2long("255.255.255.255") << (32-$netmask)));
}

function isSNMPable($device)
{
  global $config;

  $time_start = microtime(true);
  $pos = snmp_get($device, "sysObjectID.0", "-Oqv", "SNMPv2-MIB");
  $time_end = microtime(true);

  if ($pos === '' || $pos === false)
  {
    return 0;
  } else {
    $time_snmp = $time_end - $time_start;
    $time_snmp *= 1000;
    // SNMP response time in milliseconds.
    /// Note, it's full SNMP get/response time (not only UDP request).
    $time_snmp = number_format($time_snmp, 2, '.', '');
    return $time_snmp;
  }
}

/**
 *
 * It's fully BOOLEAN safe function.
 *
 */
function isPingable($hostname)
{
  global $config;

  if (filter_var($config['ping']['timeout'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 50, 'max_range' => 2000 ))))
  {
    $timeout = $config['ping']['timeout'];
  } else {
    $timeout = 500;
  }
  if (filter_var($config['ping']['retries'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 10 ))))
  {
    $retries = $config['ping']['retries'];
  } else {
    $retries = 3;
  }
  $sleep = floor(1000000 / $retries); // interval between retries, max 1 sec

  //$ping_debug = TRUE; $file = '/tmp/pings_debug.log'; $time = date('Y-m-d H:i:s', time()); /// Uncomment this line for DEBUG isPingable()

  // First try IPv4
  $ip = gethostbyname($hostname);
  if ($ip && $ip != $hostname)
  {
    $cmd = $config['fping'] . " -t $timeout -c 1 -q $ip 2>&1";
  } else {
    $ip = gethostbyname6($hostname);
    // Second try IPv6
    if ($ip)
    {
      $cmd = $config['fping6'] . " -t $timeout -c 1 -q $ip 2>&1";
    } else {
      // No DNS records
      if ($ping_debug) { file_put_contents($file, "$time | DNS ERROR: $hostname | NO DNS record found" . PHP_EOL, FILE_APPEND); }
      return 0;
    }
  }

  for ($i=1; $i <= $retries; $i++)
  {
    exec($cmd, $output, $return);
    if ($return === 0)
    {
      // normal $output[0] = '8.8.8.8 : xmt/rcv/%loss = 1/1/0%, min/avg/max = 1.21/1.21/1.21'
      $tmp = explode('/', $output[0]);
      $ping = $tmp[7];
      /// FIXME. Mike: I do not know, maybe it is, but just in case the protection from zero.
      if (!$ping) $ping = 0.01;
    } else {
      $ping = 0;
    }
    if ($ping) break;

    if ($ping_debug)
    {
      file_put_contents($file, "$time | PING ERROR: $hostname ($i) | FPING OUT: " . $output[0] . PHP_EOL, FILE_APPEND);
      if ($i == $retries) {
        $mtr = $config['mtr'] . " -r -n -c 5 $ip";
        file_put_contents($file, "MTR OUT: " . `$mtr` . PHP_EOL, FILE_APPEND);
      }
    }

    if ($i < $retries) usleep($sleep);
  }

  return $ping;
}

function is_odd($number)
{
  return $number & 1; // 0 = even, 1 = odd
}

function utime()
{
  return microtime(true);
}

function createHost($host, $community = NULL, $snmpver, $port = 161, $transport = 'udp', $v3 = array())
{
  $host = trim(strtolower($host));

  $device = array('hostname' => $host,
                  'sysName' => $host,
                  'community' => $community,
                  'port' => $port,
                  'transport' => $transport,
                  'status' => '1',
                  'snmpver' => $snmpver
            );

  $device = array_merge($device, $v3);

  $device['os']          = get_device_os($device);
  $device['sysName']     = snmp_get($device, "sysName.0", "-Oqv", "SNMPv2-MIB");
  $device['location']    = snmp_get($device, "sysLocation.0", "-Oqv", "SNMPv2-MIB");
  $device['sysContact']  = snmp_get($device, "sysContact.0", "-Oqv", "SNMPv2-MIB");

  if ($device['os'])
  {
    $device_id = dbInsert($device, 'devices');
    if ($device_id)
    {
      echo("Discovering ".$device['hostname']." (".$device_id.")");
      $device['device_id'] = $device_id;
      // Discover things we need when linking this to other hosts.
      discover_device($device, $options = array('m' => 'ports'));
      discover_device($device, $options = array('m' => 'ipv4-addresses'));
      discover_device($device, $options = array('m' => 'ipv6-addresses'));
      array_push($GLOBAL['devices'], $device_id);
      return($device_id);
    }
    else
    {
      return FALSE;
    }
  }
  else
  {
    return FALSE;
  }
}

function isDomainResolves($domain)
{
  return (gethostbyname($domain) != $domain || count(dns_get_record($domain)) != 0);
}

function hoststatus($id)
{
  return dbFetchCell("SELECT `status` FROM `devices` WHERE `device_id` = ?", array($id));
}

function match_network($nets, $ip, $first=false)
{
  $return = false;
  if (!is_array ($nets)) $nets = array ($nets);
  foreach ($nets as $net)
  {
    $rev = (preg_match ("/^\!/", $net)) ? true : false;
    $net = preg_replace ("/^\!/", "", $net);
    $ip_arr  = explode('/', $net);
    $net_long = ip2long($ip_arr[0]);
    $x        = ip2long($ip_arr[1]);
    $mask    = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
    $ip_long  = ip2long($ip);
    if ($rev)
    {
      if (($ip_long & $mask) == ($net_long & $mask)) return false;
    } else {
      if (($ip_long & $mask) == ($net_long & $mask)) $return = true;
      if ($first && $return) return true;
    }
  }

  return $return;
}

// Convert HEX IP value to pretty string:
// IPv4 "C1 9C 5A 26" => "193.156.90.38"
// IPv6 "20 01 07 F8 00 12 00 01 00 00 00 00 00 05 02 72" => "2001:07f8:0012:0001:0000:0000:0005:0272"
// IPv6 "20:01:07:F8:00:12:00:01:00:00:00:00:00:05:02:72" => "2001:07f8:0012:0001:0000:0000:0005:0272"
function hex2ip($ip_snmp)
{
  $ip = trim(str_replace(':', ' ', $ip_snmp));
  if (!isHexString($ip)) { return $ip_snmp; };
  
  $ip_array = explode(' ', $ip);
  if (count($ip_array) == 4)
  {
    // IPv4
    $ip = hexdec($ip_array[0]).'.'.hexdec($ip_array[1]).'.'.hexdec($ip_array[2]).'.'.hexdec($ip_array[3]);
  } else {
    // IPv6
    $ip = str_replace(' ', '', strtolower($ip));
    $ip = substr(preg_replace('/([a-f\d]{4})/', "$1:", $ip), 0, -1);
  }
  return $ip;
}

// Convert IP string to HEX value:
// IPv4 "193.156.90.38" => "C1 9C 5A 26"
// IPv6 "2001:07f8:0012:0001:0000:0000:0005:0272" => "20 01 07 f8 00 12 00 01 00 00 00 00 00 05 02 72"
// IPv6 "2001:7f8:12:1::5:0272" => "20 01 07 f8 00 12 00 01 00 00 00 00 00 05 02 72"
/// Note. Return lowercase string.
function ip2hex($ip, $separator = ' ')
{
  $ip = trim($ip);
  $ip_hex = '';
  if (strstr($ip, ':'))
  {
    //IPv6
    $ip_hex = str_replace(':', '', Net_IPv6::uncompress($ip, TRUE));
    $ip_hex = preg_replace('/([a-f\d]{2})/i', "$1$separator", $ip_hex);
  } else {
    //IPv4
    foreach (explode('.', $ip) as $dec)
    {
      $ip_hex .= zeropad(dechex($dec)) . $separator;
    }
  }

  $ip_hex = substr(strtolower($ip_hex), 0, -1);
  if ($ip_hex)
  {
    return $ip_hex;
  } else {
    return $ip;
  }
}


function snmp2ipv6($ipv6_snmp)
{
  $ipv6 = explode('.',$ipv6_snmp);

  // Workaround stupid Microsoft bug in Windows 2008 -- this is fixed length!
  // < fenestro> "because whoever implemented this mib for Microsoft was ignorant of RFC 2578 section 7.7 (2)"
  if (count($ipv6) == 17 && $ipv6[0] == 16)
  {
    array_shift($ipv6);
  }

  for ($i = 0;$i <= 15;$i++) { $ipv6[$i] = zeropad(dechex($ipv6[$i])); }
  for ($i = 0;$i <= 15;$i+=2) { $ipv6_2[] = $ipv6[$i] . $ipv6[$i+1]; }

  return implode(':',$ipv6_2);
}

function ipv62snmp($ipv6)
{
  $ipv6_ex = explode(':',Net_IPv6::uncompress($ipv6));
  for ($i = 0;$i < 8;$i++) { $ipv6_ex[$i] = zeropad($ipv6_ex[$i],4); }
  $ipv6_ip = implode('',$ipv6_ex);
  for ($i = 0;$i < 32;$i+=2) $ipv6_split[] = hexdec(substr($ipv6_ip,$i,2));

  return implode('.',$ipv6_split);
}

function get_astext($asn)
{
  global $config,$cache;

  // Fetch pre-set AS text from config first
  if (isset($config['astext'][$asn]))
  {
    return $config['astext'][$asn];
  }
  else
  {
    // Not preconfigured, check cache before doing a new DNS request
    if (isset($cache['astext'][$asn]))
    {
      return $cache['astext'][$asn];
    }
    else
    {
      $result = dns_get_record("AS$asn.asn.cymru.com",DNS_TXT);
      $txt = explode('|',$result[0]['txt']);
      $result = trim(str_replace('"', '', $txt[4]));
      $cache['astext'][$asn] = $result;

      return $result;
    }
  }
}

// Use this function to write to the eventlog table
function log_event($text, $device = NULL, $type = NULL, $reference = NULL)
{
  global $debug;

  if (!is_array($device)) { $device = device_by_id_cache($device); }

  $insert = array('device_id' => ($device['device_id'] ? $device['device_id'] : "NULL"),
                  'reference' => ($reference ? $reference : "NULL"),
                  'type' => ($type ? $type : "NULL"),
                  'timestamp' => array("NOW()"),
                  'message' => $text);

  dbInsert($insert, 'eventlog');
}

// Parse string with emails. Return array with email (as key) and name (as value)
function parse_email($emails)
{
  $result = array();
  $regex = '/^[\"\']?([^\"\']+)[\"\']?\s{0,}<([^@]+@[^>]+)>$/';
  if (is_string($emails))
  {
    $emails = preg_split('/[,;]\s{0,}/', $emails);
    foreach ($emails as $email)
    {
      if (preg_match($regex, $email, $out, PREG_OFFSET_CAPTURE))
      {
        $result[$out[2][0]] = $out[1][0];
      } else {
        if (strpos($email, "@")) { $result[$email] = NULL; }
      }
    }
  } else {
    // Return FALSE if input not string
    return FALSE;
  }
  return $result;
}

function notify($device,$title,$message)
{
  /// NOTE. Need full rewrite to universal function with message queues and multi-protocol (email,jabber,twitter)
  global $config, $debug;

  if ($config['alerts']['email']['enable'] && !$device['ignore'])
  {
    if (!get_dev_attrib($device,'disable_notify'))
    {
      if ($config['alerts']['email']['default_only'])
      {
        $email = $config['alerts']['email']['default'];
      } else {
        if (get_dev_attrib($device,'override_sysContact_bool'))
        {
          $email = get_dev_attrib($device,'override_sysContact_string');
        }
        elseif ($device['sysContact'])
        {
          $email = $device['sysContact'];
        } else {
          $email = $config['alerts']['email']['default'];
        }
      }
      $emails = parse_email($email);
      
      if ($emails)
      {
        // Mail backend params
        $params = array('localhost' => php_uname('n'));
        $backend = strtolower(trim($config['email_backend']));
        switch ($backend) {
          case 'sendmail':
            $params['sendmail_path'] = $config['email_sendmail_path'];
            break;
          case 'smtp':
            $params['host']     = $config['email_smtp_host'];
            $params['port']     = $config['email_smtp_port'];
            if ($config['email_smtp_secure'] == 'ssl')
            {
              $params['host']   = 'ssl://'.$config['email_smtp_host'];
              if ($config['email_smtp_port'] == 25) {
                $params['port'] = 465; // Default port for SSL
              }
            }
            $params['timeout']  = $config['email_smtp_timeout'];
            $params['auth']     = $config['email_smtp_auth'];
            $params['username'] = $config['email_smtp_username'];
            $params['password'] = $config['email_smtp_password'];
            if ($debug) { $params['debug'] = TRUE; }
            break;
          default:
            $backend = 'mail'; // Default mailer backend
        }

        // Mail headers
        $headers = array();
        if (empty($config['email_from']))
        {
          $headers['From']   = '"Observium" <observium@'.$params['localhost'].'>'; // Default "From:"
        } else {
          foreach (parse_email($config['email_from']) as $from => $from_name)
          {
            $headers['From'] = (empty($from_name)) ? $from : '"'.$from_name.'" <'.$from.'>'; // From:
          }
        }
        $rcpts_full = '';
        $rcpts = '';
        foreach ($emails as $to => $to_name)
        {
          $rcpts_full .= (empty($to_name)) ? $to.', ' : '"'.$to_name.'" <'.$to.'>, ';
          $rcpts .= $to.', ';
        }
        $rcpts_full = substr($rcpts_full, 0, -2); // To:
        $rcpts = substr($rcpts, 0, -2);
        $headers['Subject']  = $title; // Subject:
        $headers['X-Priority'] = 3; // Mail priority
        $headers['X-Mailer'] = 'Observium ' . $config['version']; // X-Mailer:
        $headers['Message-ID'] = '<' . md5(uniqid(time())) . '@' . $params['localhost'] . '>';
        $headers['Date'] = date('r', time());

        // Mail body
        $message_header = $config['page_title_prefix']."\n\n";
        $message_footer = "\n\nE-mail sent to: ".$rcpts."\n";
        $message_footer .= "E-mail sent at: " . date($config['timestamp_format']) . "\n";
        $body = $message_header . $message . $message_footer;

        // Create mailer instance
        $mail =& Mail::factory($backend, $params);
        // Sending email
        $status = $mail->send($rcpts_full, $headers, $body);
        if (PEAR::isError($status)) { echo 'Mailer Error: ' . $status->getMessage() . PHP_EOL; }
      }
    }
  }
}

// By Greg Winiarski of ditio.net
// http://ditio.net/2008/11/04/php-string-to-hex-and-hex-to-string-functions/
// We claim no copyright over this function and assume that it is free to use.

function hex2str($hex)
{
  $string='';

  for ($i = 0; $i < strlen($hex)-1; $i+=2)
  {
    $string .= chr(hexdec($hex[$i].$hex[$i+1]));
  }

  return $string;
}

// Convert an SNMP hex string to regular string
function snmp_hexstring($hex)
{
  return hex2str(str_replace(' ','',str_replace(' 00','',$hex)));
}

// Check if the supplied string is an SNMP hex string
function isHexString($str)
{
  return preg_match("/^[a-f0-9][a-f0-9]( [a-f0-9][a-f0-9])*$/is",trim($str));
}

// Include all .inc.php files in $dir
function include_dir($dir, $regex = "")
{
  global $device, $config, $debug, $valid;

  if ($regex == "")
  {
    $regex = "/\.inc\.php$/";
  }

  if ($handle = opendir($config['install_dir'] . '/' . $dir))
  {
    while (false !== ($file = readdir($handle)))
    {
      if (filetype($config['install_dir'] . '/' . $dir . '/' . $file) == 'file' && preg_match($regex, $file))
      {
        if ($debug) { echo("Including: " . $config['install_dir'] . '/' . $dir . '/' . $file . "\n"); }

        include($config['install_dir'] . '/' . $dir . '/' . $file);
      }
    }

    closedir($handle);
  }
}

function is_port_valid($port, $device)
{
  global $config, $debug;

  if (strstr($port['ifDescr'], "irtual"))
  {
    $valid = 0;
  } else {
    $valid = 1;
    $if = strtolower($port['ifDescr']);
    foreach ($config['bad_if'] as $bi)
    {
      if (strstr($if, $bi))
      {
        $valid = 0;
        if ($debug) { echo("ignored : $bi : $if"); }
      }
    }

    if (is_array($config['bad_if_regexp']))
    {
      foreach ($config['bad_if_regexp'] as $bi)
      {
        if (preg_match($bi ."i", $if))
        {
          $valid = 0;
          if ($debug) { echo("ignored : $bi : ".$if); }
        }
      }
    }

    if (is_array($config['bad_iftype']))
    {
      foreach ($config['bad_iftype'] as $bi)
      {
      if (strstr($port['ifType'], $bi))
        {
          $valid = 0;
          if ($debug) { echo("ignored ifType : ".$port['ifType']." (matched: ".$bi." )"); }
        }
      }
    }
    if (empty($port['ifDescr']) && empty($port['ifName'])) { $valid = 0; }
    if ($device['os'] == "catos" && strstr($if, "vlan")) { $valid = 0; }
  }

  return $valid;
}

# Parse CSV files with or without header, and return a multidimensional array
function parse_csv($content, $has_header = 1, $separator = ",")
{
  $lines = explode("\n", $content);
  $result = array();

  # If the CSV file has a header, load up the titles into $headers
  if ($has_header)
  {
    $headcount = 1;
    $header = array_shift($lines);
    foreach (explode($separator,$header) as $heading)
    {
      $headers[$headcount] = trim($heading);
      $headcount++;
    }
  }

  # Process every line
  foreach ($lines as $line)
  {
    $entrycount = 1;
    foreach (explode($separator,$line) as $entry)
    {
      # If we use header, place the value inside the named array entry
      # Otherwise, just stuff it in numbered fields in the array
      if ($has_header)
      {
        $line_array[$headers[$entrycount]] = trim($entry);
      } else {
        $line_array[] = trim($entry);
      }
      $entrycount++;
    }

    # Add resulting line array to final result
    $result[] = $line_array; unset($line_array);
  }

  return $result;
}

?>
