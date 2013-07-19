<?php

// Push $_GET into $vars to be compatible with web interface naming

$total_start = utime();

foreach ($_GET as $name => $value)
{
  $vars[$name] = $value;
}

preg_match('/^(?P<type>[a-z0-9A-Z-]+)_(?P<subtype>.+)/', $vars['type'], $graphtype);

if($debug) print_r($graphtype);

$type = $graphtype['type'];
$subtype = $graphtype['subtype'];

if(is_numeric($vars['device']))
{
  $device = device_by_id_cache($vars['device']);
} elseif(!empty($vars['device'])) {
  $device = device_by_name($vars['device']);
}

// FIXME -- remove these

// $from, $to - unixtime (or rrdgraph time interval, i.e. '-1d', '-6w')
// $timestamp_from, $timestamp_to - timestamps formatted as 'Y-m-d H:i:s'
$timestamp_pattern = '/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';
if (isset($vars['timestamp_from']) && preg_match($timestamp_pattern, $vars['timestamp_from']))
{
  $vars['from'] = strtotime($vars['timestamp_from']);
}
if (isset($vars['timestamp_to']) && preg_match($timestamp_pattern, $vars['timestamp_to']))
{
  $vars['to'] = strtotime($vars['timestamp_to']);
}

$from     = (isset($vars['from'])) ? $vars['from'] : time() - 86400;
$to       = (isset($vars['to'])) ? $vars['to'] : time();

if ($from < 0) { $from = $to + $from; }

$period = $to - $from;

$prev_from = $from - $period;

$graphfile = $config['temp_dir'] . "/"  . strgen() . ".png";

if (is_file($config['html_dir'] . "/includes/graphs/$type/$subtype.inc.php"))
{

  if (isset($config['allow_unauth_graphs']) && $config['allow_unauth_graphs'])
  {
    $auth = "1"; // hardcode auth for all with config function
  }

  if (isset($config['allow_unauth_graphs_cidr']) && count($config['allow_unauth_graphs_cidr']) > 0)
  {
    foreach ($config['allow_unauth_graphs_cidr'] as $range)
    {
      list($net, $mask) = explode('/', trim($range));
      if (Net_IPv4::validateIP($net))
      {
        // IPv4
        $mask = ($mask != NULL) ? $mask : '32';
        $range = $net.'/'.$mask;
        if ($mask >= 0 && $mask <= 32 && Net_IPv4::ipInNetwork($_SERVER['REMOTE_ADDR'], $range))
        {
          $auth = 1;
          if ($debug) { echo("matched $range"); }
          break;
        }
      }
      elseif (Net_IPv6::checkIPv6($net))
      {
        // IPv6
        $mask = ($mask != NULL) ? $mask : '128';
        $range = $net.'/'.$mask;
        if ($mask >= 0 && $mask <= 128 && Net_IPv6::isInNetmask($_SERVER['REMOTE_ADDR'], $range))
        {
          $auth = 1;
          if ($debug) { echo("matched $range"); }
          break;
        }
      }
    }
  }

  include($config['html_dir'] . "/includes/graphs/$type/auth.inc.php");

  if (isset($auth) && $auth)
  {
    include($config['html_dir'] . "/includes/graphs/$type/$subtype.inc.php");
  }

}
else
{
  graph_error($type.'_'.$subtype); //Graph Template Missing");
}

function graph_error($string)
{
  global $vars, $config, $debug, $graphfile;

  $vars['bg'] = "FFBBBB";

  include("includes/graphs/common.inc.php");

  $rrd_options .= " HRULE:0#555555";
  $rrd_options .= " --title='".$string."'";

  rrdtool_graph($graphfile, $rrd_options);

  if ($height > "99")  {
    $woo = shell_exec($rrd_cmd);
    if ($debug) { echo("<pre>".$rrd_cmd."</pre>"); }
    if (is_file($graphfile) && !$debug)
    {
      header('Content-type: image/png');
      $fd = fopen($graphfile,'r'); fpassthru($fd); fclose($fd);
      unlink($graphfile);
#      exit();
    }
  } else {
    if (!$debug) { header('Content-type: image/png'); }
    $im     = imagecreate($width, $height);
    $orange = imagecolorallocate($im, 255, 225, 225);
    $px     = (imagesx($im) - 7.5 * strlen($string)) / 2;
    imagestring($im, 3, $px, $height / 2 - 8, $string, imagecolorallocate($im, 128, 0, 0));
    imagepng($im);
    imagedestroy($im);
#    exit();
  }
}

if ($error_msg) {
  // We have an error :(

  graph_error($graph_error);

} elseif (!$auth) {
  // We are unauthenticated :(

  if ($width < 200)
  {
   graph_error("No Auth");
  } else {
   graph_error("No Authorization");
  }
} else {
  #$rrd_options .= " HRULE:0#999999";
  if ($no_file)
  {

    if ($width < 200)
    {
      graph_error("No RRD");
    } else {
      graph_error("Missing RRD Datafile");
    }

  } elseif($command_only) {

    $graph_start = utime();
    $return = rrdtool_graph($graphfile, $rrd_options);
    $graph_end = utime(); $graph_run = $graph_end - $graph_start; $graph_time = substr($graph_run, 0, 5);
    $total_end = utime(); $total_run = $total_end - $total_start; $total_time = substr($total_run, 0, 5);

    unlink($graphfile);

    $graph_return['total_time'] = $total_time;
    $graph_return['rrdtool_time'] = $graph_time;
    $graph_return['cmd']  = "rrdtool graph $graphfile $rrd_options";

  } else {

    if ($rrd_options)
    {
      rrdtool_graph($graphfile, $rrd_options);
      if ($debug) { echo($rrd_cmd); }
      if (is_file($graphfile))
      {
        if ($vars['image_data_uri'] == TRUE)
        {
          $image_data_uri = data_uri($graphfile,'image/png');
        } elseif (!$debug)
        {
          header('Content-type: image/png');
          $fd = fopen($graphfile,'r');fpassthru($fd);fclose($fd);
        } else {
          echo(`ls -l $graphfile`);
          echo('<img src="'.data_uri($graphfile,'image/png').'" alt="graph" />');
        }
        unlink($graphfile);
      }
      else
      {
        if ($width < 200)
        {
          graph_error("Draw Error");
        }
        else
        {
          graph_error("Error Drawing Graph");
        }
      }
    }
    else
    {
      if ($width < 200)
      {
        graph_error("Def Error");
      } else {
        graph_error("Graph Definition Error");
      }
    }
  }
}

?>
