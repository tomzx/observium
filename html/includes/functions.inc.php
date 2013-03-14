<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage functions
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

include("../includes/alerts.inc.php");


/**
 * Humanize Device
 *
 *   Process the $device array to add/modify elements.
 *
 * @param array $device
 * @return none
 */

function humanize_device(&$device)
{

  global $config;

  // Set the HTML class and Tab color for the device based on status
  if ($device['status'] == '0')
  {
    $device['html_row_class'] = "error";
    $device['html_tab_colour'] = "#cc0000";
  } else {
    $device['html_row_class'] = "";
    /// This one looks too bright and out of place - adama
    #$device['html_tab_colour'] = "#194BBF";
    /// This one matches the logo. changes are not finished, lets see if we can add colour elsewhere. - adama
    $device['html_tab_colour'] = "#194B7F"; // Fucking dull gay colour, but at least there's a semicolon now - tom
                                            // Your mum's a semicolon - adama
  }
  if ($device['ignore'] == '1')
  {
    $device['html_row_class'] = "warning";
    $device['html_tab_colour'] = "#aaaaaa";
    if ($device['status'] == '1')
    {
      $device['html_row_class'] = "";
      $device['html_tab_colour'] = "#009900";
    }
  }
  if ($device['disabled'] == '1')
  {
    $device['html_row_class'] = "warning";
    $device['html_tab_colour'] = "#aaaaaa";
  }

  $device['icon'] = getImage($device);

  // Set location if it's overridden
  /// FIXME - put this in poller, for fuck sake!
  if (get_dev_attrib($device,'override_sysLocation_bool')) {  $device['location'] = get_dev_attrib($device,'override_sysLocation_string'); }

  // Set the name we print for the OS
  $device['os_text'] = $config['os'][$device['os']]['text'];

  // Mark this device as being humanized
  $device['humanized'] = TRUE;
}


/**
 * Format date string.
 *
 * This function convert date/time string to format from
 * config option $config['timestamp_format'].
 * If date/time not detected in string, function return original string.
 * Example conversions to format 'd-m-Y H:i':
 * '2012-04-18 14:25:01' -> '18-04-2012 14:25'
 * 'Star wars' -> 'Star wars'
 *
 * @param string $str
 * @return string
 */
function format_timestamp($str)
{
  global $config;
  if (($timestamp = strtotime($str)) === false) {
    return $str;
  } else {
    return date($config['timestamp_format'], $timestamp);
  }
}

function format_unixtime($timestamp)
{
  global $config;
  return date($config['timestamp_format'], $timestamp);
}


/**
 * Return array with syslog priorities.
 *
 * This function return array with syslog priority names and colors.
 *
 * @param none
 * @return array
 */
function syslog_priorities()
{
  $priorities['0'] = array('name' => 'emergencies',   'color' => '#FF0000');
  $priorities['1'] = array('name' => 'alerts',        'color' => '#EE2222');
  $priorities['2'] = array('name' => 'critical',      'color' => '#DD3333');
  $priorities['3'] = array('name' => 'errors',        'color' => '#BB4444');
  $priorities['4'] = array('name' => 'warnings',      'color' => '#AA5555');
  $priorities['5'] = array('name' => 'notifications', 'color' => '#555599');
  $priorities['6'] = array('name' => 'informational', 'color' => '#00FF00');
  $priorities['7'] = array('name' => 'debugging',     'color' => '#0000FF');
  for ($i = 8; $i < 16; $i++)
  {
    $priorities[$i] = array('name' => 'other',        'color' => '#D2D8F9');
  }
  return $priorities;
}

/**
 * Percent Colour
 *
 *   This function returns a colour based on a 0-100 value
 *   It scales from green to red from 0-100 as default.
 *
 * @param integer $percent
 * @param integer $brightness
 * @param integer $max
 * @param integer $min
 * @param integer $thirdColorHex
 * @return string
 */

function percent_colour($value,$brightness = 128, $max = 100,$min = 0, $thirdColourHex = '00')
{
    // Calculate first and second colour (Inverse relationship)
    $first = (1-($value/$max))*$brightness;
    $second = ($value/$max)*$brightness;

    // Find the influence of the middle Colour (yellow if 1st and 2nd are red and green)
    $diff = abs($first-$second);
    $influence = ($brightness-$diff)/2;
    $first = intval($first + $influence);
    $second = intval($second + $influence);

    // Convert to HEX, format and return
    $firstHex = str_pad(dechex($first),2,0,STR_PAD_LEFT);
    $secondHex = str_pad(dechex($second),2,0,STR_PAD_LEFT);

    return '#'.$secondHex . $firstHex . $thirdColourHex;

    // alternatives:
    // return $thirdColourHex . $firstHex . $secondHex; 
    // return $firstHex . $thirdColourHex . $secondHex;

}

// Old percent_colour
//function percent_colour($percent)
//{
//  $r = min(255, 5 * ($percent - 25));
//  $b = max(0, 255 - (5 * ($percent + 25)));
//
// return sprintf('#%02x%02x%02x', $r, $b, $b);
//}

function bug()
{

  echo('<div class="alert alert-error">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Bug!</strong> Please report this to the Observium development team.
</div>');

}


function data_uri($file, $mime)
{
  $contents = file_get_contents($file);
  $base64   = base64_encode($contents);
  return ('data:' . $mime . ';base64,' . $base64);
}

function nicecase($item)
{
  switch ($item)
  {
    case "dbm":
      return "dBm";
    case "mysql":
      return" MySQL";
    case "powerdns":
      return "PowerDNS";
    case "bind":
      return "BIND";
    case "ntpd":
      return "NTPd";
    case "powerdns-recursor":
      return "PowerDNS Recursor";
    default:
      return ucfirst($item);
  }
}

function toner2colour($descr, $percent)
{
  $colour = get_percentage_colours(100-$percent);

  if (substr($descr,-1) == 'C' || stripos($descr,"cyan"   ) !== false) { $colour['left'] = "55D6D3"; $colour['right'] = "33B4B1"; }
  if (substr($descr,-1) == 'M' || stripos($descr,"magenta") !== false) { $colour['left'] = "F24AC8"; $colour['right'] = "D028A6"; }
  if (substr($descr,-1) == 'Y' || stripos($descr,"yellow" ) !== false
                               || stripos($descr,"giallo" ) !== false
                               || stripos($descr,"gul"    ) !== false) { $colour['left'] = "FFF200"; $colour['right'] = "DDD000"; }
  if (substr($descr,-1) == 'K' || stripos($descr,"black"  ) !== false
                               || stripos($descr,"nero"   ) !== false) { $colour['left'] = "000000"; $colour['right'] = "222222"; }

  return $colour;
}

function generate_link($text, $vars, $new_vars = array())
{
  return '<a href="'.generate_url($vars, $new_vars).'">'.$text.'</a>';
}

function pagination($vars, $total, $per_page = 10){

        if(is_numeric($vars['pageno']))   { $page = $vars['pageno']; } else { $page = "1"; }
        if(is_numeric($vars['pagesize'])) { $per_page = $vars['pagesize']; } else { $per_page = "10"; }

        $adjacents = "5";

        $page = ($page == 0 ? 1 : $page);
        $start = ($page - 1) * $per_page;

        $prev = $page - 1;
        $next = $page + 1;
        $lastpage = ceil($total/$per_page);
        $lpm1 = $lastpage - 1;

        $pagination = "";
        if($lastpage > 1)
        {
            $pagination .= '<form action="">';
            $pagination .= '<div class="pagination pagination-centered">';

            $pagination .= '<ul><li><a href="#">Prev</a></li>';
            if ($lastpage < 7 + ($adjacents * 2))
            {
                for ($counter = 1; $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<li class='active'><a>$counter</a></li>";
                    else
                        $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $counter))."'>$counter</a></li>";
                }
            }
            elseif($lastpage > 5 + ($adjacents * 2))
            {
                if($page < 1 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<li class='active'><a>$counter</a></li>";
                        else
                            $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $counter))."'>$counter</a></li>";
                    }
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $lpm1))."'>$lpm1</a></li>";
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $lastpage))."'>$lastpage</a></li>";
                }
                elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                {
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => '1'))."'>1</a></li>";
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => '2'))."'>2</a></li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<li class='active'><a>$counter</a></li>";
                        else
                            $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $counter))."'>$counter</a></li>";
                    }
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $lpm1))."'>$lpm1</a></li>";
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $lastpage))."'>$lastpage</a></li>";
                }
                else
                {
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => '1'))."'>1</a></li>";
                    $pagination.= "<li><a href='".generate_url($vars, array('pageno' => '2'))."'>2</a></li>";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<li class='active'><a>$counter</a></li>";
                        else
                            $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $counter))."'>$counter</a></li>";
                    }
                }
            }
            if ($page < $counter - 1){
                $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $next))."'>Next</a></li>";
                $pagination.= "<li><a href='".generate_url($vars, array('pageno' => $lastpage))."'>Last</a></li>";
            }else{
                $pagination.= "<li class='active'><a>Next</a></li>";
                $pagination.= "<li class='active'><a>Last</a></li>";
            }
            $pagination.= "</ul>";

            $pagination.= '<div style="clear: none; float: right;" class="input-prepend">
                           <span class="add-on"># per page</span>
                           <select name="type" id="type" class="span1"
                           onchange="window.open(this.options[this.selectedIndex].value,\'_top\')">';


            foreach (array('10','20','50','100','500','1000') as $pagesize)
            {
              $pagination .= "<option value='".generate_url($vars, array('pagesize' => $pagesize))."'";
              if ($pagesize == $vars['pagesize']) { $pagination .= (" selected"); }
              $pagination .= ">".$pagesize."</option>";
            }
            $pagination .= '</select></div></div></form>';

        }
        return $pagination;
    }


function generate_url($vars, $new_vars = array())
{

  $vars = array_merge($vars, $new_vars);

  $url = $vars['page']."/";
  unset($vars['page']);
  /// FIXME - No idea why these appear. Try to fix them later. They're from the date picker in graphs.
  unset($vars['?preset']);
  unset($vars['preset']);

  foreach ($vars as $var => $value)
  {
    if ($value == "0" || $value != "" && strstr($var, "opt") === FALSE && is_numeric($var) === FALSE)
    {
      $url .= $var ."=".$value."/";
    }
  }

  return($url);

}

function generate_overlib_content($graph_array, $text)
{
    global $config;
    $graph_array['height'] = "100";
    $graph_array['width']  = "210";

    $overlib_content = '<div style="width: 590px;"><span style="font-weight: bold; font-size: 16px;">'.$text."</span><br />";
    foreach (array('day','week','month','year') as $period)
    {
      $graph_array['from']        = $config['time'][$period];
      $overlib_content .= generate_graph_tag($graph_array);

    }
    $overlib_content .= "</div>";

    return $overlib_content;

}

function get_percentage_colours($percentage)
{

  if ($percentage > '90') { $background['left']='c4323f'; $background['right']='C96A73'; }
  elseif ($percentage > '75') { $background['left']='bf5d5b'; $background['right']='d39392'; }
  elseif ($percentage > '50') { $background['left']='bf875b'; $background['right']='d3ae92'; }
  elseif ($percentage > '25') { $background['left']='5b93bf'; $background['right']='92b7d3'; }
  else { $background['left']='9abf5b'; $background['right']='bbd392'; }

  return($background);

}

function generate_device_url($device, $vars=array())
{
  return generate_url(array('page' => 'device', 'device' => $device['device_id']), $vars);
}

function generate_device_link_header($device, $vars=array())
{
  global $config;

  if(!$device['humanized']) { humanize_device($device); }

  if ($device['os'] == "ios") { formatCiscoHardware($device, true); }

  $contents = '
      <table class="table table-striped table-bordered table-rounded table-condensed">
        <tr class="'.$device['html_row_class'].'" style="font-size: 10pt;">
          <td style="width: 10px; background-color: '.$device['html_tab_colour'].'; margin: 0px; padding: 0px"></td>
          <td width="40" style="padding: 10px; text-align: center; vertical-align: middle;">'.getImage($device).'</td>
          <td width="200"><a href="#" class="'.$class.'" style="font-size: 15px; font-weight: bold;">'.$device['hostname'].'</a><br />'. truncate($device['location'],64, '') .'</td>
          <td>'.$device['hardware'].' <br /> '.$device['os_text'].' '.$device['version'].'</td>
          <td>'.deviceUptime($device, 'short').'<br />'.$device['sysName'].'
          </tr>
        </table>
';

  return $contents;

}

function generate_device_link_contents($device, $vars=array(), $start=0, $end=0)
{

  global $config;

  if (!$start) { $start = $config['time']['day']; }
  if (!$end)   { $end   = $config['time']['now']; }

  $contents = generate_device_link_header($device, $vars=array());

  if (isset($config['os'][$device['os']]['over']))
  {
    $graphs = $config['os'][$device['os']]['over'];
  }
  elseif (isset($device['os_group']) && isset($config['os'][$device['os_group']]['over']))
  {
    $graphs = $config['os'][$device['os_group']]['over'];
  }
  else
  {
    $graphs = $config['os']['default']['over'];
  }

  foreach ($graphs as $entry)
  {
    $graph     = $entry['graph'];
    $graphhead = $entry['text'];
    $contents .= '<div style="width: 708px">';
    $contents .= '<span style="margin-left: 5px; font-size: 12px; font-weight: bold;">'.$graphhead.'</span><br />';
    $contents .= "<img src=\"graph.php?device=" . $device['device_id'] . "&from=".$start."&to=".$end."&width=275&height=100&type=".$graph."&legend=no&draw_all=yes" . '" style="margin: 2px;">';
    $contents .= "<img src=\"graph.php?device=" . $device['device_id'] . "&from=".$config['time']['week']."&to=".$end."&width=275&height=100&type=".$graph."&legend=no&draw_all=yes" . '" style="margin: 2px;">';
    $contents .= '</div>';
  }

  return $contents;

}

function generate_device_link($device, $text=NULL, $vars=array(), $start=0, $end=0)
{
  global $config;

  $class = devclass($device);
  if (!$text) { $text = $device['hostname']; }

  $contents = generate_device_link_contents($device, $vars, $start, $end);

  $text = htmlentities($text);
  $url = generate_device_url($device, $vars);
  $link = overlib_link($url, $text, $contents, $class);

  if (!device_permitted($device['device_id']))
  {
    return $device['hostname'];
  }
  return $link;
}

function overlib_link($url, $text, $contents, $class)
{
  global $config, $link_iter;

  $link_iter++;

  /// Allow the Grinch to disable popups and destroy Christmas.
  if($config['web_mouseover'])
  {
    $output  = '<a href="'.$url.'" class="tooltip-from-data '.$class.'" data-tooltip="'.htmlspecialchars($contents).'">'.$text.'</a>';
  } else {
    $output  = '<a href="'.$url.'" class="'.$class.'">'.$text.'</a>';
  }

  return $output;
}

function generate_graph_popup($graph_array)
{
  global $config;

  // Take $graph_array and print day,week,month,year graps in overlib, hovered over graph

  $original_from = $graph_array['from'];

  $graph = generate_graph_tag($graph_array);
  $content = "<div class=list-large>".$graph_array['popup_title']."</div>";
  $content .= '<div style="width: 850px">';
  $graph_array['legend']   = "yes";
  $graph_array['height']   = "100";
  $graph_array['width']    = "340";
  $graph_array['from']     = $config['time']['day'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['week'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['month'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['year'];
  $content .= generate_graph_tag($graph_array);
  $content .= "</div>";

  $graph_array['from'] = $original_from;

  $graph_array['link'] = generate_url($graph_array, array('page' => 'graphs', 'height' => NULL, 'width' => NULL, 'bg' => NULL));

#  $graph_array['link'] = "graphs/type=" . $graph_array['type'] . "/id=" . $graph_array['id'];

  return overlib_link($graph_array['link'], $graph, $content, NULL);
}

function print_graph_popup($graph_array)
{
  echo(generate_graph_popup($graph_array));
}

function permissions_cache($user_id)
{
  $permissions = array();
  foreach (dbFetchRows("SELECT * FROM devices_perms WHERE user_id = '".$user_id."'") as $device)
  {
    $permissions['device'][$device['device_id']] = 1;
  }
  foreach (dbFetchRows("SELECT * FROM ports_perms WHERE user_id = '".$user_id."'") as $port)
  {
    $permissions['port'][$port['port_id']] = 1;
  }
  foreach (dbFetchRows("SELECT * FROM bill_perms WHERE user_id = '".$user_id."'") as $bill)
  {
    $permissions['bill'][$bill['bill_id']] = 1;
  }

  return $permissions;
}

function bill_permitted($bill_id)
{
  global $permissions;

  if ($_SESSION['userlevel'] >= "5") {
    $allowed = TRUE;
  } elseif ($permissions['bill'][$bill_id]) {
    $allowed = TRUE;
  } else {
    $allowed = FALSE;
  }

  return $allowed;
}

function port_permitted($port_id, $device_id = NULL)
{
  global $permissions;

  if (!is_numeric($device_id)) { $device_id = get_device_id_by_port_id($port_id); }

  if ($_SESSION['userlevel'] >= "5")
  {
    $allowed = TRUE;
  } elseif (device_permitted($device_id)) {
    $allowed = TRUE;
  } elseif ($permissions['port'][$port_id]) {
    $allowed = TRUE;
  } else {
    $allowed = FALSE;
  }

  return $allowed;
}

function application_permitted($app_id, $device_id = NULL)
{
  global $permissions;

  if (is_numeric($app_id))
  {
    if (!$device_id) { $device_id = get_device_id_by_app_id ($app_id); }
    if ($_SESSION['userlevel'] >= "5") {
      $allowed = TRUE;
    } elseif (device_permitted($device_id)) {
      $allowed = TRUE;
    } elseif ($permissions['application'][$app_id]) {
      $allowed = TRUE;
    } else {
      $allowed = FALSE;
    }
  } else {
    $allowed = FALSE;
  }

  return $allowed;
}

function device_permitted($device_id)
{
  global $permissions;

  if ($_SESSION['userlevel'] >= "5")
  {
    $allowed = true;
  } elseif ($permissions['device'][$device_id]) {
    $allowed = true;
  } else {
    $allowed = false;
  }

  return $allowed;
}

function print_graph_tag($args)
{
  echo(generate_graph_tag($args));
}

function generate_graph_tag($args)
{

  foreach ($args as $key => $arg)
  {
    $urlargs[] = $key."=".$arg;
  }

  return '<img src="graph.php?' . implode('&',$urlargs).'" border="0" />';
}

function generate_graph_js_state($args) {
  // we are going to assume we know roughly what the graph url looks like here.
  // TODO: Add sensible defaults
  $from   = (is_numeric($args['from'])   ? $args['from']   : 0);
  $to     = (is_numeric($args['to'])     ? $args['to']     : 0);
  $width  = (is_numeric($args['width'])  ? $args['width']  : 0);
  $height = (is_numeric($args['height']) ? $args['height'] : 0);
  $legend = str_replace("'", "", $args['legend']);

  $state = <<<STATE
<script type="text/javascript" language="JavaScript">
document.graphFrom = $from;
document.graphTo = $to;
document.graphWidth = $width;
document.graphHeight = $height;
document.graphLegend = '$legend';
</script>
STATE;

  return $state;
}

function print_percentage_bar($width, $height, $percent, $left_text, $left_colour, $left_background, $right_text, $right_colour, $right_background)
{

  if ($percent > "100") { $size_percent = "100"; } else { $size_percent = $percent; }

  $output = '
<div style="font-size:11px;">
  <div style=" width:'.$width.'px; height:'.$height.'px; background-color:#'.$right_background.';">
    <div style="width:'.$size_percent.'%; height:'.$height.'px; background-color:#'.$left_background.'; border-right:0px white solid;"></div>
    <div style="vertical-align: middle;height: '.$height.'px;margin-top:-'.($height).'px; color:#'.$left_colour .'; padding-left :4px;"><b>'.$left_text.'</b></div>
    <div style="vertical-align: middle;height: '.$height.'px;margin-top:-'.($height).'px; color:#'.$right_colour.'; padding-right:4px;text-align:right;"><b>'.$right_text.'</b></div>
  </div>
</div>';

  return $output;
}

function generate_entity_link($type, $entity, $text=NULL, $graph_type=NULL)
{
  global $config, $entity_cache;

  if (is_numeric($entity))
  {
    $entity = get_entity_by_id_cache($type, $entity);
  }

  switch($type)
  {
    case "port":
      $link = generate_port_link($entity, $text, $graph_type);
      break;
    case "storage":
      if (empty($text)) { $text = $entity['storage_descr']; }
      $link = generate_link($text, array('page' => 'device', 'device' => $entity['device_id'], 'tab' => 'health', 'metric' => 'storage'));
      break;
    default:
      $link = $entity[$type.'_id'];
  }
  return($link);
}

function generate_port_link_header($port)
{
  global $config;

  // Push through processing function to set attributes
  if(!isset($port['humanized'])) { $port = humanize_port($port); }

  $contents = '
      <table class="table table-striped table-bordered table-rounded table-condensed">
        <tr class="'.$port['row_class'].'" style="font-size: 10pt;">
          <td style="width: 10px; background-color: '.$port['table_tab_colour'].'; margin: 0px; padding: 0px"></td>
          <td style="width: 10px;"></td>
          <td width="250"><a href="#" class="'.$port['html_row_class'].'" style="font-size: 15px; font-weight: bold;">'.fixIfName($port['label']).'</a><br />'.htmlentities($port['ifAlias']).'</td>
          <td width="100">'.$port['human_speed'].'<br />'.$port['ifMtu'].'</td>
          <td>'.$port['human_type'].'<br />'.$port['human_mac'].'</td>
        </tr>
          </table>';

  return $contents;
}

function generate_port_link($port, $text = NULL, $type = NULL)
{
  global $config;

  $port = humanize_port($port);
  if (!$text) { $text = fixIfName($port['label']); }
  if ($type) { $port['graph_type'] = $type; }
  if (!isset($port['graph_type'])) { $port['graph_type'] = 'port_bits'; }

  $class = ifclass($port['ifOperStatus'], $port['ifAdminStatus']);

  if (!isset($port['os'])) { $port = array_merge($port, device_by_id_cache($port['device_id'])); }

  $content = generate_device_link_header($port);
  $content .= generate_port_link_header($port);

  $content .= "<div class=list-large>".$port['hostname']." - " . fixifName($port['label']) . "</div>";
  if ($port['ifAlias']) { $content .= $port['ifAlias']."<br />"; }
  $content .= '<div style="width: 700px">';
  $graph_array['type']     = $port['graph_type'];
  $graph_array['legend']   = "yes";
  $graph_array['height']   = "100";
  $graph_array['width']    = "275";
  $graph_array['to']       = $config['time']['now'];
  $graph_array['from']     = $config['time']['day'];
  $graph_array['id']       = $port['port_id'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['week'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['month'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['year'];
  $content .= generate_graph_tag($graph_array);
  $content .= "</div>";

  $url = generate_port_url($port);

  if (port_permitted($port['port_id'], $port['device_id'])) {
    return overlib_link($url, $text, $content, $class);
  } else {
    return fixifName($text);
  }
}

function generate_port_url($port, $vars=array())
{
  return generate_url(array('page' => 'device', 'device' => $port['device_id'], 'tab' => 'port', 'port' => $port['port_id']), $vars);
}

function generate_port_thumbnail($args)
{
  if (!$args['bg']) { $args['bg'] = "FFFFFF"; }
  $args['content'] = "<img src='graph.php?type=".$args['graph_type']."&amp;id=".$args['port_id']."&amp;from=".$args['from']."&amp;to=".$args['to']."&amp;width=".$args['width']."&amp;height=".$args['height']."&amp;bg=".$args['bg']."'>";
  echo(generate_port_link($args, $args['content']));
}

function print_optionbar_start ($height = 0, $width = 0, $marginbottom = 5)
{
#  echo("
#    <div class='rounded-5px' style='border: 1px solid #ccc; display: block; background: #eee; text-align: left; margin-top: 0px;
#    margin-bottom: ".$marginbottom."px; " . ($width ? 'max-width: ' . $width . (strstr($width,'%') ? '' : 'px') . '; ' : '') . "
#    padding: 7px 14px 8px 14px'>");

   echo(PHP_EOL . '<div class="well well-shaded">' . PHP_EOL);

}

function print_optionbar_end()
{
  echo(PHP_EOL . '  </div>' . PHP_EOL);
}

function geteventicon($message)
{
  if ($message == "Device status changed to Down") { $icon = "server_connect.png"; }
  if ($message == "Device status changed to Up") { $icon = "server_go.png"; }
  if ($message == "Interface went down" || $message == "Interface changed state to Down") { $icon = "if-disconnect.png"; }
  if ($message == "Interface went up" || $message == "Interface changed state to Up") { $icon = "if-connect.png"; }
  if ($message == "Interface disabled") { $icon = "if-disable.png"; }
  if ($message == "Interface enabled") { $icon = "if-enable.png"; }
  if (isset($icon)) { return $icon; } else { return false; }
}

function overlibprint($text)
{
  return "onmouseover=\"return overlib('" . $text . "');\" onmouseout=\"return nd();\"";
}

function humanmedia($media)
{
  array_preg_replace($rewrite_iftype, $media);
  return $media;
}

function devclass($device)
{
  if (isset($device['status']) && $device['status'] == '0') { $class = "list-device-down"; } else { $class = "list-device"; }
  if (isset($device['ignore']) && $device['ignore'] == '1')
  {
     $class = "list-device-ignored";
     if (isset($device['status']) && $device['status'] == '1') { $class = "list-device-ignored-up"; }
  }
  if (isset($device['disabled']) && $device['disabled'] == '1') { $class = "list-device-disabled"; }

  return $class;
}

function getlocations()
{
  # Fetch override locations, not through get_dev_attrib, this would be a huge number of queries
  $rows = dbFetchRows("SELECT attrib_type,attrib_value,device_id FROM devices_attribs WHERE attrib_type LIKE 'override_sysLocation%' ORDER BY attrib_type");
  foreach ($rows as $row)
  {
    if ($row['attrib_type'] == 'override_sysLocation_bool' && $row['attrib_value'] == 1)
    {
      $ignore_dev_location[$row['device_id']] = 1;
    }
    # We can do this because of the ORDER BY, "bool" will be handled before "string"
    elseif ($row['attrib_type'] == 'override_sysLocation_string' && $ignore_dev_location[$row['device_id']] == 1)
    {
      if (!in_array($row['attrib_value'],$locations)) { $locations[] = $row['attrib_value']; }
    }
  }

  # Fetch regular locations
  if ($_SESSION['userlevel'] >= '5')
  {
    $rows = dbFetchRows("SELECT D.device_id,location FROM devices AS D GROUP BY location ORDER BY location");
  } else {
    $rows = dbFetchRows("SELECT D.device_id,location FROM devices AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = ? GROUP BY location ORDER BY location", array($_SESSION['user_id']));
  }

  foreach ($rows as $row)
  {
    # Only add it as a location if it wasn't overridden (and not already there)
    if ($row['location'] != '' && !$ignore_dev_location[$row['device_id']])
    {
      if (!in_array($row['location'],$locations)) { $locations[] = $row['location']; }
    }
  }

  sort($locations);
  return $locations;
}

function foldersize($path)
{
  $total_size = 0;
  $files = scandir($path);
  $total_files = 0;

  foreach ($files as $t)
  {
    if (is_dir(rtrim($path, '/') . '/' . $t))
    {
      if ($t<>"." && $t<>"..")
      {
        $size = foldersize(rtrim($path, '/') . '/' . $t);
        $total_size += $size;
      }
    } else {
      $size = filesize(rtrim($path, '/') . '/' . $t);
      $total_size += $size;
      $total_files++;
    }
  }

  return array($total_size, $total_files);
}

function generate_ap_link($args, $text = NULL, $type = NULL)
{
  global $config;

  $args = humanize_port($args);
  if (!$text) { $text = fixIfName($args['label']); }
  if ($type) { $args['graph_type'] = $type; }
  if (!isset($args['graph_type'])) { $args['graph_type'] = 'port_bits'; }

  if (!isset($args['hostname'])) { $args = array_merge($args, device_by_id_cache($args['device_id'])); }

  $content = "<div class=list-large>".$args['text']." - " . fixifName($args['label']) . "</div>";
  if ($args['ifAlias']) { $content .= $args['ifAlias']."<br />"; }
  $content .= "<div style=\'width: 850px\'>";
  $graph_array['type']     = $args['graph_type'];
  $graph_array['legend']   = "yes";
  $graph_array['height']   = "100";
  $graph_array['width']    = "340";
  $graph_array['to']           = $config['time']['now'];
  $graph_array['from']     = $config['time']['day'];
  $graph_array['id']       = $args['accesspoint_id'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['week'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['month'];
  $content .= generate_graph_tag($graph_array);
  $graph_array['from']     = $config['time']['year'];
  $content .= generate_graph_tag($graph_array);
  $content .= "</div>";


  $url = generate_ap_url($args);
  if (port_permitted($args['interface_id'], $args['device_id'])) {
    return overlib_link($url, $text, $content, $class);
  } else {
    return fixifName($text);
  }
}

function generate_ap_url($ap, $vars=array())
{
  return generate_url(array('page' => 'device', 'device' => $ap['device_id'], 'tab' => 'accesspoint', 'ap' => $ap['accesspoint_id']), $vars);
}


?>
