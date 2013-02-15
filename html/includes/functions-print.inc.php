<?php

// Display events
function print_events($entries)
{
  global $vars;
  if (!is_array($entries)) { return FALSE; }
  
  $list = array('date' => TRUE, 'host' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device'])) { $list['host'] = TRUE; }
  if (!isset($vars['port']) || empty($vars['port'])) { $list['port'] = TRUE; }
  $string = "<table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">\n";
  $string .= "  <thead>\n";
  $string .= "    <tr>\n";
  if ($list['date']) { $string .= "      <th>Date</th>\n"; }
  if ($list['host']) { $string .= "      <th>Host</th>\n"; }
  if ($list['port']) { $string .= "      <th>Type</th>\n"; }
  $string .= "      <th>Message</th>\n";
  $string .= "    </tr>\n";
  $string .= "  </thead>\n";
  $string .= '<tbody>';

  foreach ($entries as $entry)
  {
    //$hostname = gethostbyid($entry['host']);
    $icon = geteventicon($entry['message']);
    if ($icon) { $icon = '<img src="images/16/' . $icon . '" />'; }

    $string .= "<tr>  ";
    if ($list['date']) { $string .= "<td width=\"160\">" . $entry['datetime'] . "</td>"; }
    if ($list['host']) {
      $dev = device_by_id_cache($entry['host']);
      $string .= "<td class=list-bold width=150>" . generate_device_link($dev, shorthost($dev['hostname'])) . "</td>";
    }
    if ($list['port']) {
      if ($entry['type'] == "interface")
      {
        $this_if = ifLabel(getifbyid($entry['reference']));
        $entry['link'] = "<b>" . generate_port_link($this_if, makeshortif(strtolower($this_if['label']))) . "</b>";
      } else {
        $entry['link'] = "System";
      }
      $string .= "<td>" . $entry['link'] . "</td>";
    }

    $string .= "<td>" . htmlspecialchars($entry['message']) . "</td>\n</tr>";
  }

  $string .='</tbody>';
  $string .= "</table>";

  // Here one time printing :P
  echo $string;
}

// Display events
function print_events_short($entries)
{
if (!is_array($entries)) { return FALSE; }

$string = "<table class=\"table table-bordered table-condensed table-striped table-hover table-rounded\">";

foreach ($entries as $entry)
{
  //if ($bg == $list_colour_a) { $bg = $list_colour_b; } else { $bg=$list_colour_a; }
  $icon = geteventicon($entry['message']);
  if ($icon) { $icon = "<img src='images/16/$icon'>"; }

  $string .= "<tr>";
  $string .= "<td width=0></td>";
  $string .= "<td class=syslog width=140>" . $entry['humandate'] . "</td>";
  if ($entry['type'] == "interface") {
    $entry['link'] = "<b>".generate_port_link(getifbyid($entry['reference']))."</b>";
  }
  $string .= "<td class=syslog>" . $entry['link'] . " " .  htmlspecialchars($entry['message']) . "</td>";
  $string .= "<td></td>\n</tr>";
}

$string .= "</table>";

echo $string;
}

?>