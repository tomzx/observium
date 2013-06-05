<?php

// If anybody has again the idea to implement the PHP internal library calls,
// be aware that it was tried and banned by lead dev Adam
//
// TRUE STORY. THAT SHIT IS WHACK. -- adama.


function mib_dirs($mibs)
{
  global $config;

  $dirs = array($config['mib_dir']."/rfc", $config['mib_dir']."/net-snmp");

  if(!is_array($mibs)) { $mibs = array($mibs); }

  foreach($mibs as $mib)
  {
    if(ctype_alnum($mib))
    {
      $dirs[] = $config['mib_dir']."/".$mib;
    }
  }

  return implode(":", $dirs);
}

// Crappy function to get workaround 32bit counter wrapping in HOST-RESOURCES-MIB
function snmp_dewrap32bit($value)
{
 if ( preg_match('/^-/',$value) ) {
 return (abs($value) + 2147483647);
 } else {
 return $value;
 }
}

// Translate OID string to numeric:
// 'BGP4-V2-MIB-JUNIPER::jnxBgpM2PeerRemoteAs' -> '.1.3.6.1.4.1.2636.5.1.1.2.1.1.1.13'
function snmp_translate($oid, $mib = NULL, $mibdir = NULL)
{
  // $rewrite_oids set in rewrites.php
  global $rewrite_oids, $config, $debug;

  if ($mib)
  {
    // If $mib::$oid known in $rewrite_oids use this value instead shell command snmptranslate.
    if (isset($rewrite_oids[$mib][$oid])) {
      if ($debug) { echo("TRANSLATE (REWRITE):[".$rewrite_oids[$mib][$oid]."]\n"); }
      return $rewrite_oids[$mib][$oid];
    }
    $oid = "$mib::$oid";
  }

  $cmd  = $config['snmptranslate'];
  if ($options) { $cmd .= ' ' . $options; } else { $cmd .= ' -On'; }
  if ($mib) { $cmd .= ' -m ' . $mib; }
  if ($mibdir) { $cmd .= ' -M ' . $mibdir; } else { $cmd .= ' -M ' . mib_dirs(); }
  $cmd .= ' ' . $oid;
  if (!$debug) { $cmd .= ' 2>/dev/null'; }

  $data = trim(external_exec($cmd));

  if ($data && !strstr($data, 'Unknown'))
  {
    if ($debug) { echo("TRANSLATE (CMD): $oid [".$data."]"); }
    return $data;
  } else {
    return '';
  }
}

// Take -OXsq output and parse it into an array. Fancy.
function parse_oid2($string)
{
	$result = array();
	$matches = array();

	// Match OID - If wrapped in double-quotes ('"'), must escape '"', else must escape ' ' (space) or '[' - Other escaping is optional
	$match_count = preg_match('/^(?:((?!")(?:[^\\\\\\[ ]|(?:\\\\.))+)|(?:"((?:[^\\\\\"]|(?:\\\\.))+)"))/', $string, $matches);
	if (null !== $match_count && $match_count > 0)
	{
		// [1] = unquoted, [2] = quoted
		$value = strlen($matches[1]) > 0 ? $matches[1] : $matches[2];

		$result[] = stripslashes($value);

		// I do this (vs keeping track of offset) to use ^ in regex
		$string = substr($string, strlen($matches[0]));

		// Match indexes (optional) - If wrapped in double-quotes ('"'), must escape '"', else must escape ']' - Other escaping is optional
		while (true)
		{
			$match_count = preg_match('/^\\[(?:((?!")(?:[^\\\\\\]]|(?:\\\\.))+)|(?:"((?:[^\\\\\"]|(?:\\\\.))+)"))\\]/', $string, $matches);
			if (null !== $match_count && $match_count > 0)
			{
				// [1] = unquoted, [2] = quoted
				$value = strlen($matches[1]) > 0 ? $matches[1] : $matches[2];

				$result[] = stripslashes($value);

				// I do this (vs keeping track of offset) to use ^ in regex
				$string = substr($string, strlen($matches[0]));
			}
			else
			{
				break;
			}
		} // while
		// Match value - Skips leading ' ' characters - If remainder is wrapped in double-quotes ('"'), must escape '"', othe escaping is optional
		$match_count = preg_match('/^\\s+(?:((?!")(?:[^\\\\]|(?:\\\\.))+)|(?:"((?:[^\\\\\"]|(?:\\\\.))+)"))$/', $string, $matches);
		if (null !== $match_count && $match_count > 0)
		{
			// [1] = unquoted, [2] = quoted
			$value = strlen($matches[1]) > 0 ? $matches[1] : $matches[2];

			$result[] = stripslashes($value);

			if (strlen($string) != strlen($matches[0])) { echo "Length error!"; return null; }

			return $result;
		}
	}
	// All or nothing
	return null;
}

// Take -Oqs output and parse it into an array containing OID array and the value
// Hopefully this is the beginning of more intelligent OID parsing!
// Thanks to David Farrell <DavidPFarrell@gmail.com> for the parser solution.
// This function is free for use by all with attribution to David.
function parse_oid($string)
{
    $result = array();
    while (true)
    {
        $matches = array();
        $match_count = preg_match('/^(?:((?:[^\\\\\\. "]|(?:\\\\.))+)|(?:"((?:[^\\\\"]|(?:\\\\.))+)"))((?:[\\. ])|$)/', $string, $matches);
        if (null !== $match_count && $match_count > 0)
        {
            // [1] = unquoted, [2] = quoted
            $value = strlen($matches[1]) > 0 ? $matches[1] : $matches[2];

            $result[] = stripslashes($value);

            // Are we expecting any more parts?
            if (strlen($matches[3]) > 0)
            {
                // I do this (vs keeping track of offset) to use ^ in regex
                $string = substr($string, strlen($matches[0]));
            }
            else
            {
                $ret['value'] = array_pop($result);
                $ret['oid']   = $result;
                return $ret;
            }
        }
        else
        {
            // All or nothing
            return null;
        }
    } // while
}

function string_to_oid($string)
{
  $oid = strlen($string);
  for($i = 0; $i != strlen($string); $i++)
  {
     $oid .= ".".ord($string[$i]);
  }
  return $oid;
}

// Dirty attempt to parse snmp stuff. YUCK.

function snmp_parser_quote($m) {
    return str_replace(array('.',' '),
      array('PLACEHOLDER-DOT', 'PLACEHOLDER-SPACE'), $m[1]);
}

function snmp_parser_unquote($str) {
    return str_replace(array('PLACEHOLDER-DOT', 'PLACEHOLDER-SPACE', 'PLACEHOLDER-ESCAPED-QUOTE'),
      array('.',' ','"'), $str);
}

function snmp_get_multi($device, $oids, $options = "-OQUs", $mib = NULL, $mibdir = NULL)
{
  global $debug,$config,$runtime_stats,$mibs_loaded;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
     $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout'])) {
     $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport']))
  {
    $device['transport'] = "udp";
  }

  $cmd  = $config['snmpget'];
  $cmd .= snmp_gen_auth ($device);

  if ($options) { $cmd .= " " . $options; }
  if ($mib) { $cmd .= " -m " . $mib; }
  if ($mibdir) { $cmd .= " -M " . $mibdir; } else { $cmd .= " -M " . $config['mib_dir']; }

  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }

  $cmd .= " ".$device['transport'].":".$device['hostname'].":".$device['port'];
  $cmd .= " ".$oids;
  if (!$debug) { $cmd .= " 2>/dev/null"; }
  $data = trim(external_exec($cmd));
  $runtime_stats['snmpget']++;
  foreach (explode("\n", $data) as $entry)
  {
    list($oid,$value) = explode("=", $entry, 2);
    $oid = trim($oid); $value = trim($value);
    list($oid, $index) = explode(".", $oid);
    if (!strstr($value, "at this OID") && isset($oid) && isset($index))
    {
      $array[$index][$oid] = $value;
    }
  }

  return $array;
}

function snmp_get($device, $oid, $options = NULL, $mib = NULL, $mibdir = NULL)
{
  global $debug,$config,$runtime_stats,$mibs_loaded;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
     $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout'])) {
     $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport']))
  {
    $device['transport'] = "udp";
  }

  if (strstr($oid,' '))
  {
    echo("BUG: snmp_get called for multiple OIDs: $oid\n");
    echo("Please report this to the Observium team.");
  }

  $cmd  = $config['snmpget'];
  $cmd .= snmp_gen_auth ($device);

  if ($options) { $cmd .= " " . $options; }
  if ($mib) { $cmd .= " -m " . $mib; }
  if ($mibdir) { $cmd .= " -M " . $mibdir; } else { $cmd .= " -M ".$config['mib_dir']; }
  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }
  $cmd .= " " . $device['transport'].":".$device['hostname'].":".$device['port'];
  $cmd .= " " . $oid;

  if (!$debug) { $cmd .= " 2>/dev/null"; }
  $data = trim(external_exec($cmd));

  $runtime_stats['snmpget']++;

  if (is_string($data) && (preg_match("/(No Such Instance|No Such Object|No more variables left|Authentication failure)/i", $data)))
  {
    return false;
  }
  elseif (!empty($data) || is_numeric($data)) { return $data; }
  else { return false; }
}

function snmp_walk_parser2($device, $oid, $oid_elements, $mib = NULL, $mibdir = NULL)
{
  $data = snmp_walk($device, $oid, "-Oqs", $mib, $mibdir, FALSE);
  foreach (explode("\n", $data) as $text) {
    $ret = parse_oid2($text);
    if (!empty($ret['value'])) {
      // this seems retarded. need a way to just build this automatically.
      switch ($oid_elements) {
        case "1":
          $array[$ret['oid'][0]] = $ret['value'];
          break;
        case "2":
          $array[$ret['oid'][1]][$ret['oid'][0]] = $ret['value'];
          break;
        case "3":
          $array[$ret['oid'][1]][$ret['oid'][2]][$ret['oid'][0]] = $ret['value'];
          break;
        case "4":
          $array[$ret['oid'][1]][$ret['oid'][2]][$ret['oid'][3]][$ret['oid'][0]] = $ret['value'];
          break;
      }
    }
  }
  return $array;
}


function snmp_walk_parser($device, $oid, $oid_elements, $mib = NULL, $mibdir = NULL)
{
  $data = snmp_walk($device, $oid, "-Oqs", $mib, $mibdir, FALSE);
  foreach (explode("\n", $data) as $text) {
    $ret = parse_oid($text);
    if (!empty($ret['value'])) {
      // this seems retarded. need a way to just build this automatically.
      switch ($oid_elements) {
        case "1":
          $array[$ret['oid'][0]] = $ret['value'];
          break;
        case "2":
          $array[$ret['oid'][1]][$ret['oid'][0]] = $ret['value'];
          break;
        case "3":
          $array[$ret['oid'][1]][$ret['oid'][2]][$ret['oid'][0]] = $ret['value'];
          break;
        case "4":
          $array[$ret['oid'][1]][$ret['oid'][2]][$ret['oid'][3]][$ret['oid'][0]] = $ret['value'];
          break;
      }
    }
  }
  return $array;
}

function snmp_walk($device, $oid, $options = NULL, $mib = NULL, $mibdir = NULL, $strip_quotes = 1)
{
  global $debug,$config,$runtime_stats;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
    $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout']))
  {
    $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0) {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];  }

  if (!isset($device['transport']))  {
    $device['transport'] = "udp";  }

  if ($device['snmpver'] == 'v1' || (isset($config['os'][$device['os']]['nobulk']) && $config['os'][$device['os']]['nobulk']))
  {
    $snmpcommand = $config['snmpwalk'];
  } else {
    $snmpcommand = $config['snmpbulkwalk'];
    if($config['snmp']['max-rep'] == TRUE && is_numeric($config['os'][$device['os']]['snmp']['max-rep']))
    {
      $snmpcommand .= ' -Cr'.$config['os'][$device['os']]['snmp']['max-rep'];
    }
  }

  $cmd = $snmpcommand." ".snmp_gen_auth($device);

  if ($options) { $cmd .= " $options "; }
  if ($mib) { $cmd .= " -m $mib"; }
  if ($mibdir) { $cmd .= " -M " . $mibdir; } else { $cmd .= " -M " . $config['mib_dir']; }
  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }

  $cmd .= " ".$device['transport'].":".$device['hostname'].":".$device['port']." ".$oid;

  if (!$debug) { $cmd .= " 2>/dev/null"; }
  $data = trim(external_exec($cmd));
  if ($strip_quotes) { $data = str_replace("\"", "", $data); }

  if (is_string($data) && (preg_match("/No Such (Object|Instance)/i", $data)))
  {
    $data = false;
  }
  else
  {
    if (preg_match("/No more variables left in this MIB View \(It is past the end of the MIB tree\)$/",$data))  {
    # Bit ugly :-(
    $d_ex = explode("\n",$data);
    unset($d_ex[count($d_ex)-1]);
    $data = implode("\n",$d_ex);
    }
  }
  $runtime_stats['snmpwalk']++;

  return $data;
}

function snmpwalk_cache_cip($device, $oid, $array, $mib = 0)
{
  global $config;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
    $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout']))
  {
    $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport'])) { $device['transport'] = "udp"; }

  if ($device['snmpver'] == 'v1' || $config['os'][$device['os']]['nobulk'])
  {
    $snmpcommand = $config['snmpwalk'];
  }
  else
  {
    $snmpcommand = $config['snmpbulkwalk'];
  }

  $cmd = $snmpcommand;
  $cmd .= snmp_gen_auth ($device);

  $cmd .= " -O snQ";
  if ($mib) { $cmd .= " -m $mib"; }
  $cmd .= " -M " . $config['mib_dir'];
  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }

  $cmd .= " ".$device['transport'].":".$device['hostname'].":".$device['port']." ".$oid;

  if (!$debug) { $cmd .= " 2>/dev/null"; }
  $data = trim(external_exec($cmd));
  $device_id = $device['device_id'];

  #echo("Caching: $oid\n");
  foreach (explode("\n", $data) as $entry)
  {
    list ($this_oid, $this_value) = preg_split("/=/", $entry);
    $this_oid = trim($this_oid);
    $this_value = trim($this_value);
    $this_oid = substr($this_oid, 30);
    list($ifIndex,$dir,$a,$b,$c,$d,$e,$f) = explode(".", $this_oid);
    $h_a = zeropad(dechex($a));
    $h_b = zeropad(dechex($b));
    $h_c = zeropad(dechex($c));
    $h_d = zeropad(dechex($d));
    $h_e = zeropad(dechex($e));
    $h_f = zeropad(dechex($f));
    $mac = "$h_a$h_b$h_c$h_d$h_e$h_f";
    if ($dir == "1") { $dir = "input"; } elseif ($dir == "2") { $dir = "output"; }
    if ($mac && $dir)
    {
      $array[$ifIndex][$mac][$oid][$dir] = $this_value;
    }
  }
  return $array;
}

function snmp_cache_ifIndex($device)
{
  // FIXME: this is not yet using our own snmp_*
  global $config;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
    $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout']))
  {
    $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport'])) { $device['transport'] = "udp"; }

  if ($device['snmpver'] == 'v1' || $config['os'][$device['os']]['nobulk'])
  {
    $snmpcommand = $config['snmpwalk'];
  }
  else
  {
    $snmpcommand = $config['snmpbulkwalk'];
  }

  $cmd = $snmpcommand;
  $cmd .= snmp_gen_auth ($device);

  $cmd .= " -O Qs";
  $cmd .= " -M " . $config['mib_dir'];
  $cmd .= " -m IF-MIB ifIndex";

  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }
  if (!$debug) { $cmd .= " 2>/dev/null"; }
  $data = trim(external_exec($cmd));
  $device_id = $device['device_id'];

  foreach (explode("\n", $data) as $entry)
  {
    list ($this_oid, $this_value) = preg_split("/=/", $entry);
    list ($this_oid, $this_index) = explode(".", $this_oid);
    $this_index = trim($this_index);
    $this_oid = trim($this_oid);
    $this_value = trim($this_value);
    if (!strstr($this_value, "at this OID") && $this_index)
    {
      $array[] = $this_value;
    }
  }

  return $array;
}

function snmpwalk_values($device, $oid, $array, $mib = NULL, $mibdir = NULL)
{
  $data = snmp_walk($device, $oid, "-OQUs", $mib, $mibdir);
  foreach (explode("\n", $data) as $entry)
  {
    list($oid,$value) = explode("=", $entry, 2);
    $oid = trim($oid); $value = trim($value);
    list($oid, $index) = explode(".", $oid, 2);
    if (!strstr($value, "at this OID") && isset($oid) && isset($index))
    {
      $array[] = $value;
    }
  }

  return $array;
}


function snmpwalk_numericoids($device, $oid, $array, $mib = NULL, $mibdir = NULL)
{
  $data = snmp_walk($device, $oid, "-OQUn", $mib, $mibdir);
  foreach (explode("\n", $data) as $entry)
  {
    list($oid,$value) = explode("=", $entry, 2);
    $oid = trim($oid); $value = trim($value);
    if (!strstr($value, "at this OID") && isset($oid))
    {
      $array[$oid] = $value;
    }
  }

  return $array;
}

function snmpwalk_cache_oid($device, $oid, $array, $mib = NULL, $mibdir = NULL, $set_cache = FALSE)
{
  global $cache;
   
  if (!(is_array($cache['snmp'][$device['device_id']]) && array_key_exists($oid,$cache['snmp'][$device['device_id']])))
  {
    $data = snmp_walk($device, $oid, "-OQUs", $mib, $mibdir);
    foreach (explode("\n", $data) as $entry)
    {
      list($oid,$value) = explode("=", $entry, 2);
      $oid = trim($oid); $value = trim($value);
      list($oid, $index) = explode(".", $oid, 2);
      if (!strstr($value, "at this OID") && isset($oid) && isset($index))
      {
        $array[$index][$oid] = $value;
      }
    }
    if ($set_cache)
    {
      $cache['snmp'][$device['device_id']][$oid] = $array;
    } else {
      return $array;
    }
  }
  return $cache['snmp'][$device['device_id']][$oid];
}

// just like snmpwalk_cache_oid except that it returns the numerical oid as the index
// this is useful when the oid is indexed by the mac address and snmpwalk would
// return periods (.) for non-printable numbers, thus making many different indexes appear
// to be the same.
function snmpwalk_cache_oid_num($device, $oid, $array, $mib = NULL, $mibdir = NULL, $set_cache = FALSE)
{
  global $cache;
   
  if (!(is_array($cache['snmp'][$device['device_id']]) && array_key_exists($oid,$cache['snmp'][$device['device_id']])))
  {
    $data = snmp_walk($device, $oid, "-OQUn", $mib, $mibdir);
    foreach (explode("\n", $data) as $entry)
    {
      list($oid,$value) = explode("=", $entry, 2);
      $oid = trim($oid); $value = trim($value);
      list($oid, $index) = explode(".", $oid, 2);
      if (!strstr($value, "at this OID") && isset($oid) && isset($index))
      {
        $array[$index][$oid] = $value;
      }
    }
    if ($set_cache)
    {
      $cache['snmp'][$device['device_id']][$oid] = $array;
    } else {
      return $array;
    }
  }
  return $cache['snmp'][$device['device_id']][$oid];
}

// just like snmpwalk_cache_oid_num (it returns the numerical oid as the index),
// but use snmptranslate for cut mib part from index
/// FIXME. maybe override function snmpwalk_cache_oid_num()?
function snmpwalk_cache_oid_num2($device, $oid, $array, $mib = NULL, $mibdir = NULL, $set_cache = FALSE)
{
  global $cache;
   
  if (!(is_array($cache['snmp'][$device['device_id']]) && array_key_exists($oid,$cache['snmp'][$device['device_id']])))
  {
    $data = snmp_walk($device, $oid, '-OQUn', $mib, $mibdir);

    $translate = snmp_translate($oid, $mib, $mibdir);
    $pattern = '/^' . str_replace('.', '\.', $translate) . '\./';

    foreach (explode("\n", $data) as $entry)
    {
      list($oid_num, $value) = explode("=", $entry, 2);
      $oid_num = trim($oid_num); $value = trim($value);
      $index = preg_replace($pattern, '', $oid_num);
      
      if (!strstr($value, "at this OID") && isset($oid) && isset($index))
      {
        $array[$index][$oid] = $value;
      }
    }
    if ($set_cache)
    {
      $cache['snmp'][$device['device_id']][$oid] = $array;
    } else {
      return $array;
    }
  }
  return $cache['snmp'][$device['device_id']][$oid];
}

function snmpwalk_cache_multi_oid($device, $oid, $array, $mib = NULL, $mibdir = NULL)
{
  global $cache;

  if (!(is_array($cache['snmp'][$device['device_id']]) && array_key_exists($oid,$cache['snmp'][$device['device_id']])))
  {
    $data = snmp_walk($device, $oid, "-OQUs", $mib, $mibdir);
    foreach (explode("\n", $data) as $entry)
    {
      list($r_oid,$value) = explode("=", $entry, 2);
      $r_oid = trim($r_oid); $value = trim($value);
      $oid_parts = explode(".", $r_oid);
      $r_oid = $oid_parts['0'];
      $index = $oid_parts['1'];
      if (isset($oid_parts['2'])) { $index .= ".".$oid_parts['2']; }
      if (isset($oid_parts['3'])) { $index .= ".".$oid_parts['3']; }
      if (isset($oid_parts['4'])) { $index .= ".".$oid_parts['4']; }
      if (isset($oid_parts['5'])) { $index .= ".".$oid_parts['5']; }
      if (isset($oid_parts['6'])) { $index .= ".".$oid_parts['6']; }
      if (!strstr($value, "at this OID") && isset($r_oid) && isset($index))
      {
        $array[$index][$r_oid] = $value;
      }
    }
    $cache['snmp'][$device['device_id']][$oid] = $array;
  }

  return $cache['snmp'][$device['device_id']][$oid];
}

function snmpwalk_cache_double_oid($device, $oid, $array, $mib = NULL, $mibdir = NULL)
{
  $data = snmp_walk($device, $oid, "-OQUs", $mib, $mibdir);

  foreach (explode("\n", $data) as $entry)
  {
    list($oid,$value) = explode("=", $entry, 2);
    $oid = trim($oid); $value = trim($value);
    list($oid, $first, $second) = explode(".", $oid);
    if (!strstr($value, "at this OID") && isset($oid) && isset($first) && isset($second))
    {
      $double = $first.".".$second;
      $array[$double][$oid] = $value;
    }
  }

  return $array;
}

function snmpwalk_cache_triple_oid($device, $oid, $array, $mib = NULL, $mibdir = NULL)
{
  $data = snmp_walk($device, $oid, "-OQUs", $mib, $mibdir);

  foreach (explode("\n", $data) as $entry)
  {
    list($oid,$value) = explode("=", $entry, 2);
    $oid = trim($oid); $value = trim($value);
    list($oid, $first, $second, $third) = explode(".", $oid);
    if (!strstr($value, "at this OID") && isset($oid) && isset($first) && isset($second))
    {
      $index = $first.".".$second.".".$third;
      $array[$index][$oid] = $value;
    }
  }

  return $array;
}

function snmpwalk_cache_twopart_oid($device, $oid, $array, $mib = NULL, $mibdir = NULL)
{
  global $config, $debug;

  $data = snmp_walk($device, $oid, "-OQUs", $mib, $mibdir);

  foreach (explode("\n", $data) as $entry)
  {
    list($oid,$value) = explode("=", $entry, 2);
    $oid = trim($oid); $value = trim($value); $value = str_replace("\"", "", $value);
    list($oid, $first, $second) = explode(".", $oid);
    if (!strstr($value, "at this OID") && isset($oid) && isset($first) && isset($second))
    {
      $array[$first][$second][$oid] = $value;
    }
  }

  return $array;
}

function snmpwalk_cache_threepart_oid($device, $oid, $array, $mib = NULL, $mibdir = NULL)
{
  global $config, $debug;

  $data = snmp_walk($device, $oid, "-OQUs", $mib, $mibdir);

  foreach (explode("\n", $data) as $entry)
  {
    list($oid,$value) = explode("=", $entry, 2);
    $oid = trim($oid); $value = trim($value); $value = str_replace("\"", "", $value);
    list($oid, $first, $second, $third) = explode(".", $oid);
    if ($debug) {echo("$entry || $oid || $first || $second || $third\n"); }
    if (!strstr($value, "at this OID") && isset($oid) && isset($first) && isset($second) && isset($third))
    {
      $array[$first][$second][$third][$oid] = $value;
    }
  }

  return $array;
}

function snmp_cache_slotport_oid($oid, $device, $array, $mib = 0)
{
  global $config;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
     $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout'])) {
     $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport']))
  {
    $device['transport'] = "udp";
  }

  if ($device['snmpver'] == 'v1' || $config['os'][$device['os']]['nobulk'])
  {
    $snmpcommand = $config['snmpwalk'];
  }
  else
  {
    $snmpcommand = $config['snmpbulkwalk'];
  }

  $cmd = $snmpcommand;
  $cmd .= snmp_gen_auth ($device);

  $cmd .= " -O QUs";
  if ($mib) { $cmd .= " -m $mib"; }
  $cmd .= " -M " . $config['mib_dir'];
  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }
  $cmd .= " ".$device['transport'].":".$device['hostname'].":".$device['port']." ".$oid;
  if (!$debug) { $cmd .= " 2>/dev/null"; }
  $data = trim(external_exec($cmd));
  $device_id = $device['device_id'];

  foreach (explode("\n", $data) as $entry)
  {
    $entry = str_replace($oid.".", "", $entry);
    list($slotport, $value) = explode("=", $entry, 2);
    $slotport = trim($slotport); $value = trim($value);
    if ($array[$slotport]['ifIndex'])
    {
      $ifIndex = $array[$slotport]['ifIndex'];
      $array[$ifIndex][$oid] = $value;
    }
  }

  return $array;
}

function snmp_cache_oid($oid, $device, $array, $mib = 0)
{
  $array = snmpwalk_cache_oid($device, $oid, $array, $mib);
  return $array;
}

function snmp_cache_port_oids($oids, $port, $device, $array, $mib=0)
{
  global $config;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
     $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout'])) {
     $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport']))
  {
    $device['transport'] = "udp";
  }

  foreach ($oids as $oid)
  {
    $string .= " $oid.$port";
  }

  $cmd = $config['snmpget'];
  $cmd .= snmp_gen_auth ($device);

  $cmd .= " -O vq";
  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }
  $cmd .= " -M " . $config['mib_dir'];
  if ($mib) { $cmd .= " -m $mib"; }
  $cmd .= " -t " . $timeout . " -r " . $retries;
  $cmd .= " ".$device['transport'].":".$device['hostname'].":".$device['port']." ".$string;
  if (!$debug) { $cmd .= " 2>/dev/null"; }
  $data = trim(external_exec($cmd));
  $x=0;
  $values = explode("\n", $data);
  #echo("Caching: ifIndex $port\n");
  foreach ($oids as $oid) {
    if (!strstr($values[$x], "at this OID"))
    {
      $array[$port][$oid] = $values[$x];
    }
    $x++;
  }

  return $array;
}

function snmp_cache_portIfIndex($device, $array)
{
  global $config;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
     $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout'])) {
     $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport']))
  {
    $device['transport'] = "udp";
  }

  $cmd = $config['snmpwalk'];
  $cmd .= snmp_gen_auth ($device);

  $cmd .= " -CI -m CISCO-STACK-MIB -O q";
  $cmd .= " -M " . $config['mib_dir'];
  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }
  $cmd .= " ".$device['transport'].":".$device['hostname'].":".$device['port']." portIfIndex";
  $output = trim(external_exec($cmd));
  $device_id = $device['device_id'];

  foreach (explode("\n", $output) as $entry)
  {
    $entry = str_replace("CISCO-STACK-MIB::portIfIndex.", "", $entry);
    list($slotport, $ifIndex) = explode(" ", $entry);
    if ($slotport && $ifIndex) {
      $array[$ifIndex]['portIfIndex'] = $slotport;
      $array[$slotport]['ifIndex'] = $ifIndex;
    }
  }

  return $array;
}

function snmp_cache_portName($device, $array)
{
  global $config;

  if (is_numeric($device['timeout']) && $device['timeout'] > 0)
  {
     $timeout = $device['timeout'];
  } elseif (isset($config['snmp']['timeout'])) {
     $timeout =  $config['snmp']['timeout'];
  }

  if (is_numeric($device['retries']) && $device['retries'] > 0)
  {
    $retries = $device['retries'];
  } elseif (isset($config['snmp']['retries'])) {
    $retries =  $config['snmp']['retries'];
  }

  if (!isset($device['transport']))
  {
    $device['transport'] = "udp";
  }

  $cmd = $config['snmpwalk'];
  $cmd .= snmp_gen_auth ($device);

  $cmd .= " -CI -m CISCO-STACK-MIB -O Qs";
  $cmd .= " -M " . $config['mib_dir'];
  if (isset($timeout)) { $cmd .= " -t " . $timeout; }
  if (isset($retries)) { $cmd .= " -r " . $retries; }
  $cmd .= " ".$device['transport'].":".$device['hostname'].":".$device['port']." portName";
  $output = trim(external_exec($cmd));
  $device_id = $device['device_id'];
  #echo("Caching: portName\n");

  foreach (explode("\n", $output) as $entry)
  {
    $entry = str_replace("portName.", "", $entry);
    list($slotport, $portName) = explode("=", $entry, 2);
    $slotport = trim($slotport); $portName = trim($portName);
    if ($array[$slotport]['ifIndex'])
    {
      $ifIndex = $array[$slotport]['ifIndex'];
      $array[$slotport]['portName'] = $portName;
      $array[$ifIndex]['portName'] = $portName;
    }
  }

  return $array;
}

function snmp_gen_auth (&$device)
{
  global $debug;

  $cmd = '';
  $vlan = FALSE;
  if (isset($device['snmpcontext']))
  {
    if (is_numeric($device['snmpcontext']) && $device['snmpcontext'] > 0 && $device['snmpcontext'] < 4096 )
    {
      $vlan = $device['snmpcontext'];
    }
  }
  switch($device['snmpver'])
  {
    case 'v3':
      $cmd = ' -v3 -l ' . $device['authlevel'];
      /* NOTE.
       * For proper work of 'vlan-' context on cisco, it is necessary to add 'match prefix' in snmp-server config --mike
       * example: snmp-server group MONITOR v3 auth match prefix access SNMP-MONITOR
       */
      $cmd .= ($vlan) ? ' -n "vlan-' . $vlan . '"' : ' -n ""'; // Some devices, like HP, always require option '-n'

      switch($device['authlevel'])
      {
        case 'authPriv':
          $cmd .= ' -x ' . $device['cryptoalgo'];
          $cmd .= ' -X "' . $device['cryptopass'] . '"';
        case 'authNoPriv':
          $cmd .= ' -a ' . $device['authalgo'];
          $cmd .= ' -A "' . $device['authpass'] . '"';
          $cmd .= ' -u ' . $device['authname'];
          break;
        case 'noAuthNoPriv':
          // We have to provide a username anyway (see Net-SNMP doc)
          $cmd .= ' -u observium';
          break;
        default:
          if ($debug) { print 'DEBUG: ' . $device['authlevel'] . ' : Unsupported SNMPv3 AuthLevel.' . PHP_EOL; }
      }
      break;

    case 'v2c':
    case 'v1':
      $cmd  = ' -' . $device['snmpver'];
      $cmd .= ' -c ' . $device['community'];
      if ($vlan) { $cmd .= '@' . $vlan; }
      break;
    default:
      if ($debug) { print 'DEBUG: ' . $device['snmpver'] . ' : Unsupported SNMP Version.' . PHP_EOL; }
  }

  if ($debug) { print "DEBUG: SNMP Auth options = $cmd\n"; }

  return $cmd;
}

?>
