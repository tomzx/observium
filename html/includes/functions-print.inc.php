<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @author     Mike Stupalov <mike@stupalov.ru>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.0.3
 *
 */

/**
 * Display events.
 *
 * Display pages with device/port/system events on some formats.
 * Examples:
 * print_events() - display last 10 events from all devices
 * print_events(array('pagesize' => 99)) - display last 99 events from all device
 * print_events(array('pagesize' => 10, 'pageno' => 3, 'pagination' => TRUE)) - display 10 events from page 3 with pagination header
 * print_events(array('pagesize' => 10, 'device' = 4)) - display last 10 events for device_id 4
 * print_events(array('short' => TRUE)) - show small block with last events
 *
 * @param array $vars
 * @return none
 *
 * @author Mike Stupalov <mike@stupalov.ru>
 */
function print_events($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']) ? TRUE : FALSE;
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']) ? TRUE : FALSE;
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
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

  $list = array('device' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'eventlog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['port']) || empty($vars['port'])) { $list['port'] = TRUE; }

  $string = "<table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">\n";
  if (!$short)
  {
    $string .= "  <thead>\n";
    $string .= "    <tr>\n";
    $string .= "      <th>Date</th>\n";
    if ($list['device']) { $string .= "      <th>Device</th>\n"; }
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
    if ($list['device'])
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

/**
 * Display syslog messages.
 *
 * Display pages with device syslog messages.
 * Examples:
 * print_events() - display last 10 syslog messages from all devices
 * print_events(array('pagesize' => 99)) - display last 99 syslog messages from all device
 * print_events(array('pagesize' => 10, 'pageno' => 3, 'pagination' => TRUE)) - display 10 syslog messages from page 3 with pagination header
 * print_events(array('pagesize' => 10, 'device' = 4)) - display last 10 syslog messages for device_id 4
 * print_events(array('short' => TRUE)) - show small block with last syslog messages
 *
 * @param array $vars
 * @return none
 *
 * @author Mike Stupalov <mike@stupalov.ru>
 */
function print_syslogs($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']) ? TRUE : FALSE;
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']) ? TRUE : FALSE;
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $prioritys = syslog_prioritys();
  
  $param = array();
  $where = " WHERE 1 ";
  foreach ($vars as $var => $value)
  {
    if ($value != "")
    {
      switch ($var)
      {
        case 'device':
          $where .= " AND `device_id` = ?";
          $param[] = $value;
          break;
        case 'priority':
        case 'program':
          if ($value === '[[EMPTY]]') { $value = ""; }
          $where .= " AND `$var` = ?";
          $param[] = $value;
          break;
        case 'message':
          foreach(explode(",", $value) as $val)
          {
            $param[] = "%".$val."%";
            $cond[] = "`msg` LIKE ?";
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
    $query = "SELECT * FROM `syslog` " . $where . " ORDER BY `timestamp` DESC LIMIT $start,$pagesize";
    $query_count = "SELECT COUNT(*) FROM `syslog` " . $where;
  } else {
    $query = "SELECT * FROM `syslog` AS E, devices_perms AS P " . $where . " AND E.device_id = P.device_id AND P.user_id = ? ORDER BY `timestamp` DESC LIMIT $start,$pagesize";
    $query_count = "SELECT COUNT(*) FROM `syslog` AS E, devices_perms AS P " . $where . " AND E.device_id = P.device_id AND P.user_id = ?";
    $param[] = $_SESSION['user_id'];
  }
  // Query syslog messages
  $entries = dbFetchRows($query, $param);
  // Query syslog count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'priority' => TRUE); // For now (temporarily) priority always displayed
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'syslog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['priority']) || empty($vars['priority'])) { $list['priority'] = TRUE; }

  $string = "<table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">\n";
  if (!$short)
  {
    $string .= "  <thead>\n";
    $string .= "    <tr>\n";
    $string .= "      <th>Date</th>\n";
    if ($list['device']) { $string .= "      <th>Device</th>\n"; }
    if ($list['priority']) { $string .= "      <th>Priority</th>\n"; }
    $string .= "      <th>Message</th>\n";
    $string .= "    </tr>\n";
    $string .= "  </thead>\n";
  }
  $string .= '<tbody>';

  foreach ($entries as $entry)
  {
    $string .= "<tr>";
    if ($short)
    {
      $string .= "<td width=\"160\" class=\"syslog\">";
    } else {
      $string .= "<td width=\"160\">";
    }
    $string .= format_timestamp($entry['timestamp']) . "</td>";
    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['device_id']);
      $string .= "<td class=\"list-bold\" width=150>" . generate_device_link($dev, shorthost($dev['hostname'])) . "</td>";
    }
    if ($list['priority'])
    {
      if (!$short) { $string .= "<td style=\"color: " . $prioritys[$entry['priority']]['color'] . ";\">(" . $entry['priority'] . ")&nbsp;" . $prioritys[$entry['priority']]['name'] . "</td>"; }
    }
    if ($short)
    {
      $string .= "<td class=\"syslog\">";
    } else {
      $string .= "<td>";
    }
    $entry['program'] = ($entry['program'] === '') ? "[[EMPTY]]" : $entry['program'];
    $string .= "<strong>" . $entry['program'] . " :</strong> " . htmlspecialchars($entry['msg']) . "</td>\n</tr>";
  }

  $string .= '</tbody>';
  $string .= "</table>";

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print events
  echo $string;
}

/**
 * Display status events.
 *
 * Display pages with events about device troubles.
 * Examples:
 * print_status(array('devices' => TRUE)) - display for devices down
 *
 * Another statuses:
 * devices, uptime, ports, errors, services, bgp
 *
 * @param array $status
 * @return none
 *
 * @author Mike Stupalov <mike@stupalov.ru>
 */
function print_status($status)
{
  // Mike: I know that there are duplicated variables, but later will remove global
  global $config;

  $string = "        <table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">";
  $string .= "            <thead>";
  $string .= "                <tr>";
  $string .= "                    <th>Device</th>";
  $string .= "                    <th>Type</th>";
  $string .= "                    <th>Status</th>";
  $string .= "                    <th>Entity</th>";
  $string .= "                    <th>Location</th>";
  $string .= "                    <th>Time Since / Information</th>";
  $string .= "                </tr>";
  $string .= "            </thead>";
  $string .= "            <tbody>";

  $param = array();
  if ($_SESSION['userlevel'] >= '7')
  {
    $query_perms = ' ';
    $query_user = '';
  } else {
    $query_perms = ', devices_perms AS P ';
    $query_user = ' AND D.device_id = P.device_id AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }
  $query_device = " AND D.ignore = '0' AND D.disabled = '0'"; // Don't show ignored and disabled devices

  $empty_line = "<tr><td colspan=6></td></tr>"; // FIXME here :)
  
  // Show Device Status
  if ($status['devices'])
  {
    $query = "SELECT * FROM `devices` AS D" . $query_perms;
    $query .= "WHERE D.status = '0'" . $query_device . $query_user;
    $query .= "ORDER BY D.hostname ASC";
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $device)
    {
      $string .= "                <tr>";
      $string .= "                    <td nowrap>".generate_device_link($device, $device['hostname'])."</td>";
      $string .= "                    <td><span class=\"badge badge-inverse\">Device</span></td>";
      $string .= "                    <td><span class=\"label label-important\">Device Down</span></td>";
      $string .= "                    <td>-</td>";
      $string .= "                    <td nowrap>".substr($device['location'], 0, 30)."</td>";
      $string .= "                    <td nowrap>".deviceUptime($device, 'short')."</td>";
      $string .= "                </tr>";
    }
    if (!empty($entries)) { $string .= $empty_line; }
  }

  // Uptime
  if ($status['uptime'])
  {
    if (filter_var($config['uptime_warning'], FILTER_VALIDATE_FLOAT) !== FALSE && $config['uptime_warning'] > 0)
    {
      $query = "SELECT * FROM `devices` AS D" . $query_perms;
      $query .= "WHERE D.status = '1' AND D.uptime > 0 AND D.uptime < '" . $config['uptime_warning'] . "'" . $query_device . $query_user;
      $query .= "ORDER BY D.hostname ASC";
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $device)
      {
        $string .= "                <tr>";
        $string .= "                    <td nowrap>".generate_device_link($device, $device['hostname'])."</td>";
        $string .= "                    <td><span class=\"badge badge-inverse\">Device</span></td>";
        $string .= "                    <td><span class=\"label label-success\">Device Rebooted</span></td>";
        $string .= "                    <td>-</td>";
        $string .= "                    <td nowrap>".substr($device['location'], 0, 30)."</td>";
        $string .= "                    <td nowrap>Uptime ".formatUptime($device['uptime'], 'short')."</td>";
        $string .= "                </tr>";
      }
      if (!empty($entries)) { $string .= $empty_line; }
    }
  }

  // Ports Down
  if ($status['ports'])
  {
    $query = "SELECT * FROM `ports` AS I, `devices` AS D" . $query_perms;
    $query .= "WHERE I.device_id = D.device_id AND I.ifOperStatus = 'down' AND I.ifAdminStatus = 'up' AND I.ignore = '0' AND I.deleted = '0'" . $query_device . $query_user;
    $query .= "ORDER BY D.hostname ASC, I.ifDescr * 1 ASC";
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $port)
    {
      $port = ifNameDescr($port);
      $string .= "                <tr>";
      $string .= "                    <td nowrap>".generate_device_link($port, $port['hostname'])."</td>";
      $string .= "                    <td><span class=\"badge badge-info\">Port</span></td>";
      $string .= "                    <td><span class=\"label label-important\">Port Down</span></td>";
      $string .= "                    <td nowrap>".generate_port_link($port, $port['label'])."</td>";
      $string .= "                    <td nowrap>".substr($port['location'], 0, 30)."</td>";
      $string .= "                    <td nowrap>Down for ".formatUptime($config['time']['now'] - strtotime($port['ifLastChange']), 'short')."</td>"; // This is like deviceUptime()
      $string .= "                </tr>";
    }
    if (!empty($entries)) { $string .= $empty_line; }
  }

  // Ports Errors (only deltas)
  if ($status['errors'])
  {
    $query = "SELECT * FROM `ports` AS I, `ports-state` AS E, `devices` AS D" . $query_perms;
    $query .= "WHERE I.device_id = D.device_id AND I.ifOperStatus = 'up' AND I.ignore = '0' AND I.deleted = '0' AND I.port_id = E.port_id AND (E.ifInErrors_delta > 0 OR E.ifOutErrors_delta > 0)" . $query_device . $query_user;
    $query .= "ORDER BY D.hostname ASC, I.ifDescr * 1 ASC";
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $port)
    {
      $port = ifNameDescr($port);
      $string .= "                <tr>";
      $string .= "                    <td nowrap>".generate_device_link($port, $port['hostname'])."</td>";
      $string .= "                    <td><span class=\"badge badge-info\">Port</span></td>";
      $string .= "                    <td><span class=\"label label-important\">Port Errors</span></td>";
      $string .= "                    <td nowrap>".generate_port_link($port, $port['label'])."</td>";
      $string .= "                    <td nowrap>".substr($port['location'], 0, 30)."</td>";
      $string .= "                    <td>Errors ";
      if($port['ifInErrors_delta']) { $string .= "In: ".$port['ifInErrors_delta']; }
      if($port['ifInErrors_delta'] && $port['ifOutErrors_delta']) { $string .= ", "; }
      if($port['ifOutErrors_delta']) { $string .= "Out: ".$port['ifOutErrors_delta']; }
      $string .= "</td>";
      $string .= "                </tr>";
    }
    if (!empty($entries)) { $string .= $empty_line; }
  }

  // Services
  if ($status['services'])
  {
    $query = "SELECT * FROM `services` AS S, `devices` AS D" . $query_perms;
    $query .= "WHERE S.device_id = D.device_id AND S.service_status = 'down' AND S.service_ignore = '0'" . $query_device . $query_user;
    $query .= "ORDER BY D.hostname ASC";
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $service)
    {
      $string .= "                <tr>";
      $string .= "                    <td nowrap>".generate_device_link($service, $service['hostname'])."</td>";
      $string .= "                    <td><span class=\"badge\">Service</span></td>";
      $string .= "                    <td><span class=\"label label-important\">Service Down</span></td>";
      $string .= "                    <td>".$service['service_type']."</td>";
      $string .= "                    <td nowrap>".substr($service['location'], 0, 30)."</td>";
      $string .= "                    <td nowrap>Down for ".formatUptime($config['time']['now'] - strtotime($service['service_changed']), 'short')."</td>"; // This is like deviceUptime()
      $string .= "                </tr>";
    }
    if (!empty($entries)) { $string .= $empty_line; }
  }

  // BGP
  if ($status['bgp'])
  {
    if (isset($config['enable_bgp']) && $config['enable_bgp'])
    {
      // Description for BGP states
      $bgpstates = "IDLE - Router is searching routing table to see whether a route exists to reach the neighbor. &#xA;";
      $bgpstates .= "CONNECT - Router found a route to the neighbor and has completed the three-way TCP handshake. &#xA;";
      $bgpstates .= "OPEN SENT - Open message sent, with parameters for the BGP session. &#xA;";
      $bgpstates .= "OPEN CONFIRM - Router received agreement on the parameters for establishing session. &#xA;";
      $bgpstates .= "ACTIVE - Router didn't receive agreement on parameters of establishment. &#xA;";
      //$bgpstates .= "ESTABLISHED - Peering is established; routing begins.";

      $query = "SELECT * FROM `devices` AS D, bgpPeers AS B" . $query_perms;
      $query .= "WHERE bgpPeerAdminStatus = 'start' AND bgpPeerState != 'established' AND B.device_id = D.device_id" . $query_device . $query_user;
      $query .= "ORDER BY D.hostname ASC";
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $peer)
      {
        $string .= "                <tr>";
        $string .= "                    <td nowrap>".generate_device_link($peer, $peer['hostname'])."</td>";
        $string .= "                    <td><span class=\"badge badge-warning\">BGP</span></td>";
        $string .= "                    <td><span class=\"label label-important\" title=\"".$bgpstates."\">BGP ".strtoupper($peer['bgpPeerState'])."</span></td>";
        $string .= "                    <td nowrap>".$peer['bgpPeerIdentifier']."</td>";
        $string .= "                    <td nowrap>".substr($peer['location'], 0, 30)."</td>";
        $string .= "                    <td nowrap><strong>AS".$peer['bgpPeerRemoteAs']." :</strong> ". substr($peer['astext'], 0, 15)."</td>";
        $string .= "                </tr>";
      }
      if (!empty($entries)) { $string .= $empty_line; }
    }
  }

  $string .= "            </tbody>";
  $string .= "        </table>";
  
  // Final print all statuses
  echo($string);

}

?>
