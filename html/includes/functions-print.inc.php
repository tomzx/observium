<?php

/**
 * Display events.
 *
 * Display pages with device/port/system events on some formats.
 * Examples:
 * print_events() - display last 10 events from all devices
 * print_events(array('pagesize' => 99)) - display last 99 events from all device
 * print_events(array('pagesize' => 10, 'pageno' => 3, 'pagination' => TRUE)) - display 10 events from page 3 with pagination header
 * print_events(array('pagesize' => 10, 'host' = 4)) - display last 10 events for device_id 4
 * print_events(array('short' => TRUE)) - show small block with last events
 *
 * @param array $vars
 * @return none
 *
 * @author Mike Stupalov <mike@stupalov.ru>
 */
function print_events($vars = array('pagesize' => 10))
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']) ? TRUE : FALSE;
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']) ? TRUE : FALSE;
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = $vars['pagesize'];
  $start = $pagesize * $pageno - $pagesize;

  $param = array();
  $where = " WHERE 1 ";
  foreach ($vars as $var => $value)
  {
    if ($value != "")
    {
      switch ($var)
      {
        case 'device':
          $where .= " AND `host` = ?";
          $param[] = $value;
          break;
        case 'port':
          $where .= " AND `reference` = ?";
          $param[] = $value;
          break;
        case 'type':
          $where .= " AND `$var` = ?";
          $param[] = $value;
          break;
        case 'message':
          foreach(explode(",", $value) as $val)
          {
            $param[] = "%".$val."%";
            $cond[] = "`$var` LIKE ?";
          }
          $where .= "AND (";
          $where .= implode(" OR ", $cond);
          $where .= ")";
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= '7')
  {
    $query = "SELECT * FROM `eventlog` AS E " . $where . " ORDER BY `datetime` DESC LIMIT $start,$pagesize";
    $query_count = "SELECT COUNT(*) FROM `eventlog`" . $where;
  } else {
    $query = "SELECT * FROM `eventlog` AS E, devices_perms AS P " . $where . " AND E.host = P.device_id AND P.user_id = ? ORDER BY `datetime` DESC LIMIT $start,$pagesize";
    $query_count = "SELECT COUNT(*) FROM `eventlog` AS E, devices_perms AS P " . $where . " AND E.host = P.device_id AND P.user_id = ?";
    $param[] = $_SESSION['user_id'];
  }
  // Query events
  $entries = dbFetchRows($query, $param);
  // Query events count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('host' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'eventlog') { $list['host'] = TRUE; }
  if ($short || !isset($vars['port']) || empty($vars['port'])) { $list['port'] = TRUE; }

  $string = "<table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">\n";
  if (!$short)
  {
    $string .= "  <thead>\n";
    $string .= "    <tr>\n";
    $string .= "      <th>Date</th>\n";
    if ($list['host']) { $string .= "      <th>Device</th>\n"; }
    if ($list['port']) { $string .= "      <th>Entity</th>\n"; }
    $string .= "      <th>Message</th>\n";
    $string .= "    </tr>\n";
    $string .= "  </thead>\n";
  }
  $string .= '<tbody>';

  foreach ($entries as $entry)
  {
    $icon = geteventicon($entry['message']);
    if ($icon) { $icon = '<img src="images/16/' . $icon . '" />'; }

    $string .= "<tr>";
    if ($short)
    {
      $string .= "<td width=\"160\" class=\"syslog\">";
    } else {
      $string .= "<td width=\"160\">";
    }
    $string .= format_timestamp($entry['datetime']) . "</td>";
    if ($list['host'])
    {
      $dev = device_by_id_cache($entry['host']);
      $string .= "<td class=\"list-bold\" width=150>" . generate_device_link($dev, shorthost($dev['hostname'])) . "</td>";
    }
    if ($list['port'])
    {
      if ($entry['type'] == "interface")
      {
        $this_if = ifLabel(getifbyid($entry['reference']));
        $entry['link'] = "<b>" . generate_port_link($this_if, makeshortif(strtolower($this_if['label']))) . "</b>";
      } else {
        $entry['link'] = "System";
      }
      if (!$short) { $string .= "<td>" . $entry['link'] . "</td>"; }
    }
    if ($short)
    {
      $string .= "<td class=\"syslog\">" . $entry['link'] . " ";
    } else {
      $string .= "<td>";
    }
    $string .= htmlspecialchars($entry['message']) . "</td>\n</tr>";
  }

  $string .= '</tbody>';
  $string .= "</table>";

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print events
  echo $string;
}

/**
 * Display short events.
 *
 * This is use function:
 * print_events(array('short' => TRUE))
 * 
 * @param array $vars
 * @return none
 * 
 * @author Mike Stupalov <mike@stupalov.ru>
 */
function print_events_short($var)
{
  $var['short'] = TRUE;
  print_events($var);
}

?>
