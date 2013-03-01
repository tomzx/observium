<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage functions-print
 * @author     Mike Stupalov <mike@stupalov.ru>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.0.5
 *
 */

/**
 * Display IPv4/IPv6 addresses.
 *
 * Display pages with IP addresses from device Interfaces.
 *
 * @param array $vars
 * @return none
 *
 * @author Mike Stupalov <mike@stupalov.ru>
 * 
 */
function print_addresses($vars)
{
  // Short events? (no pagination, small out)
  // FIXME. Mike: in this funcrtion short output not used
  $short = (isset($vars['short']) && $vars['short']) ? TRUE : FALSE;
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']) ? TRUE : FALSE;
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $address_search = FALSE;
  switch($vars['search'])
  {
    case '6':
    case 'ipv6':
    case 'v6':
      $address_type = 'ipv6';
      break;
    default:
      $address_type = 'ipv4';
  }
  
  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
          $where .= ' AND I.device_id = ?';
          $param[] = $value;
          break;
        case 'interface':
          $where .= ' AND I.ifDescr LIKE ?';
          $param[] = $value;
          break;
        case 'address':
          $address_search = TRUE;
          list($addr, $mask) = explode('/', $value);
          if (!$mask) {
            $mask = ($address_type === 'ipv4') ? '32' : '128';
          }
          break;
      }
    }
  }
  if ($_SESSION['userlevel'] >= '7')
  {
    $query_perms = ' ';
    $query_user = '';
  } else {
    $query_perms = ', devices_perms AS P ';
    $query_user = ' AND D.device_id = P.device_id AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }
  $query_device = ' AND D.ignore = 0 AND D.disabled = 0 '; // Don't show ignored and disabled devices

  $query = 'FROM `'.$address_type.'_addresses` AS A, `ports` AS I, `devices` AS D, `'.$address_type.'_networks` AS N' . $query_perms;
  $query .= $where . ' AND I.port_id = A.port_id AND I.device_id = D.device_id AND N.'.$address_type.'_network_id = A.'.$address_type.'_network_id ' . $query_device . $query_user;
  $query_count = 'SELECT COUNT(*) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY A.'.$address_type.'_address';
  if ($address_search) {
    $pagination = FALSE;
  } else {
    $query .= " LIMIT $start,$pagesize";
  }

  // Query addresses
  $entries = dbFetchRows($query, $param);
  // Query address count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'search') { $list['device'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed table-rounded">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    $string .= '      <th>Interface</th>' . PHP_EOL;
    $string .= '      <th>Address</th>' . PHP_EOL;
    $string .= '      <th>Description</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $address_show = TRUE;
    if ($address_search)
    {
      if ($address_type === 'ipv4')
      {
        $address_show = Net_IPv4::ipInNetwork($entry[$address_type.'_address'], $addr . '/' . $mask);
      } else {
        $address_show = Net_IPv6::isInNetmask($entry[$address_type.'_address'], $addr, $mask);
      }
    }

    if ($address_show)
    {
      $speed = humanspeed($entry['ifSpeed']);
      
      list($prefix, $length) = explode('/', $entry[$address_type.'_network']);
     
      if (port_permitted($entry['port_id']))
      {
        $entry = ifLabel ($entry, $entry);
        if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
        {
          $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
        }
  
        $string .= '  <tr>' . PHP_EOL;
        if ($list['device'])
        {
          $string .= '    <td class="list-bold" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
        }
        $string .= '    <td class="list-bold">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
        if ($address_type === 'ipv6') { $entry[$address_type.'_address'] = Net_IPv6::compress($entry[$address_type.'_address']); }
        $string .= '    <td>' . $entry[$address_type.'_address'] . '/' . $length . '</td>' . PHP_EOL;
        $string .= '    <td>' . $entry['ifAlias'] . '</td>' . PHP_EOL;
        $string .= '  </tr>' . PHP_EOL;
      }
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print addresses
  echo $string;
}
 
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
 * @author Dennis de Houx <dennis@aio.be>, Mike Stupalov <mike@stupalov.ru>
 * 
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
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
          $where .= ' AND E.host = ?';
          $param[] = $value;
          break;
        case 'port':
          $where .= ' AND E.reference = ?';
          $param[] = $value;
          break;
        case 'type':
          $where .= ' AND E.type = ?';
          $param[] = $value;
          break;
        case 'message':
          foreach(explode(',', $value) as $val)
          {
            $param[] = '%'.$val.'%';
            $cond[] = "`$var` LIKE ?";
          }
          $where .= 'AND (';
          $where .= implode(' OR ', $cond);
          $where .= ')';
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= '7')
  {
    $query_perms = ' ';
    $query_user = '';
  } else {
    $query_perms = ', devices_perms AS P ';
    $query_user = ' AND D.device_id = P.device_id AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }
  $query_device = ' AND D.ignore = 0 AND D.disabled = 0 '; // Don't show ignored and disabled devices

  $query = 'FROM `eventlog` AS E, `devices` AS D'.$query_perms;
  $query .= $where . ' AND E.host = D.device_id '.$query_device.$query_user;
  $query_count = 'SELECT COUNT(*) '.$query;
  // FIXME Mike: bad table columns (`host` and `type`), they intersect with table `devices`
  $query = 'SELECT STRAIGHT_JOIN E.host, E.datetime, E.message, E.type, E.reference '.$query;
  $query .= " ORDER BY `datetime` DESC LIMIT $start,$pagesize";

  // Query events
  $entries = dbFetchRows($query, $param);
  // Query events count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'eventlog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['port']) || empty($vars['port'])) { $list['port'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed table-rounded">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>Date</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['port'])   { $string .= '      <th>Entity</th>' . PHP_EOL; }
    $string .= '      <th>Message</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string   .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $icon = geteventicon($entry['message']);
    if ($icon) { $icon = '<img src="images/16/' . $icon . '" />'; }
  
    $string .= '  <tr>' . PHP_EOL;
    if ($short)
    {
      $string .= '    <td width="160" class="syslog">';
    } else {
      $string .= '    <td width="160">';
    }
    $string .= format_timestamp($entry['datetime']) . '</td>' . PHP_EOL;
    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['host']);
      $string .= '    <td class="list-bold">' . generate_device_link($dev, shorthost($dev['hostname'])) . '</td>' . PHP_EOL;
    }
    if ($list['port'])
    {
      if ($entry['type'] == 'interface')
      {
        $this_if = ifLabel(getifbyid($entry['reference']), $entry);
        $entry['link'] = '<span class="list-bold">' . generate_port_link($this_if, makeshortif($this_if['label'])) . '</span>';
      } else {
        $entry['link'] = 'System';
      }
      if (!$short) { $string .= '    <td>' . $entry['link'] . '</td>' . PHP_EOL; }
    }
    if ($short)
    {
      $string .= '    <td class="syslog">' . $entry['link'] . ' ';
    } else {
      $string .= '    <td>';
    }
    $string .= htmlspecialchars($entry['message']) . '</td>' . PHP_EOL;
    $string .= '  </tr>' . PHP_EOL;
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

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
 * @author Dennis de Houx <dennis@aio.be>, Mike Stupalov <mike@stupalov.ru>
 *
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
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
          $where .= ' AND D.device_id = ?';
          $param[] = $value;
          break;
        case 'priority':
        case 'program':
          if ($value === '[[EMPTY]]') { $value = ''; }
          $where .= " AND `$var` = ?";
          $param[] = $value;
          break;
        case 'message':
          foreach(explode(',', $value) as $val)
          {
            $param[] = '%'.$val.'%';
            $cond[] = '`msg` LIKE ?';
          }
          $where .= 'AND (';
          $where .= implode(' OR ', $cond);
          $where .= ')';
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= '7')
  {
    $query_perms = ' ';
    $query_user = '';
  } else {
    $query_perms = ', devices_perms AS P ';
    $query_user = ' AND D.device_id = P.device_id AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }
  $query_device = ' AND D.ignore = 0 AND D.disabled = 0 '; // Don't show ignored and disabled devices

  $query = 'FROM `syslog` AS S, `devices` AS D'.$query_perms;
  $query .= $where . ' AND S.device_id = D.device_id '.$query_user.$query_device;
  $query_count = 'SELECT COUNT(*) '.$query;
  $query = 'SELECT STRAIGHT_JOIN * '.$query;
  $query .= " ORDER BY `timestamp` DESC LIMIT $start,$pagesize";
  
  // Query syslog messages
  $entries = dbFetchRows($query, $param);
  // Query syslog count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'priority' => TRUE); // For now (temporarily) priority always displayed
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'syslog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['priority']) || empty($vars['priority'])) { $list['priority'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed table-rounded">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>Date</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['priority']) { $string .= '      <th>Priority</th>' . PHP_EOL; }
    $string .= '      <th>Message</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $string .= '  <tr>';
    if ($short)
    {
      $string .= '    <td width="160" class="syslog">';
    } else {
      $string .= '    <td width="160">';
    }
    $string .= format_timestamp($entry['timestamp']) . '</td>' . PHP_EOL;
    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['device_id']);
      $string .= '    <td class="list-bold">' . generate_device_link($dev, shorthost($dev['hostname'])) . '</td>' . PHP_EOL;
    }
    if ($list['priority'])
    {
      if (!$short) { $string .= '    <td style="color: ' . $prioritys[$entry['priority']]['color'] . ';">(' . $entry['priority'] . ')&nbsp;' . $prioritys[$entry['priority']]['name'] . '</td>' . PHP_EOL; }
    }
    if ($short)
    {
      $string .= '    <td class="syslog">';
    } else {
      $string .= '    <td>';
    }
    $entry['program'] = ($entry['program'] === '') ? '[[EMPTY]]' : $entry['program'];
    $string .= '<strong>' . $entry['program'] . ' :</strong> ' . htmlspecialchars($entry['msg']) . '</td>' . PHP_EOL;
    $string .= '  </tr>' . PHP_EOL;
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>' . PHP_EOL;

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print events
  echo($string);
}

/**
 * Display status alerts.
 *
 * Display pages with alerts about device troubles.
 * Examples:
 * print_status(array('devices' => TRUE)) - display for devices down
 *
 * Another statuses:
 * devices, uptime, ports, errors, services, bgp
 *
 * @param array $status
 * @return none
 * 
 * @author Dennis de Houx <dennis@aio.be>, Mike Stupalov <mike@stupalov.ru>
 *
 */
function print_status($status)
{
  // Mike: I know that there are duplicated variables, but later will remove global
  global $config;
  
  $string  = '<table class="table table-bordered table-striped table-hover table-condensed table-rounded">' . PHP_EOL;
  $string .= '  <thead>' . PHP_EOL;
  $string .= '  <tr>' . PHP_EOL;
  $string .= '    <th>Device</th>' . PHP_EOL;
  $string .= '    <th>Type</th>' . PHP_EOL;
  $string .= '    <th>Status</th>' . PHP_EOL;
  $string .= '    <th>Entity</th>' . PHP_EOL;
  $string .= '    <th>Location</th>' . PHP_EOL;
  $string .= '    <th>Time Since / Information</th>' . PHP_EOL;
  $string .= '  </tr>' . PHP_EOL;
  $string .= '  </thead>' . PHP_EOL;
  $string .= '  <tbody>' . PHP_EOL;

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
  $query_device = ' AND D.ignore = 0 AND D.disabled = 0 '; // Don't show ignored and disabled devices

  // Show Device Status
  if ($status['devices'])
  {
    $query = 'SELECT * FROM `devices` AS D' . $query_perms;
    $query .= 'WHERE D.status = 0' . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $device)
    {
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="list-bold">' . generate_device_link($device, shorthost($device['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge badge-inverse">Device</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Device Down</span></td>' . PHP_EOL;
      $string .= '    <td>-</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($device['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . deviceUptime($device, 'short') . '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  // Uptime
  if ($status['uptime'])
  {
    if (filter_var($config['uptime_warning'], FILTER_VALIDATE_FLOAT) !== FALSE && $config['uptime_warning'] > 0)
    {
      $query = 'SELECT * FROM `devices` AS D' . $query_perms;
      $query .= 'WHERE D.status = 1 AND D.uptime > 0 AND D.uptime < ' . $config['uptime_warning'] . $query_device . $query_user;
      $query .= 'ORDER BY D.hostname ASC';
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $device)
      {
        $string .= '  <tr>' . PHP_EOL;
        $string .= '    <td class="list-bold">' . generate_device_link($device, shorthost($device['hostname'])) . '</td>' . PHP_EOL;
        $string .= '    <td><span class="badge badge-inverse">Device</span></td>' . PHP_EOL;
        $string .= '    <td><span class="label label-success">Device Rebooted</span></td>' . PHP_EOL;
        $string .= '    <td>-</td>' . PHP_EOL;
        $string .= '    <td nowrap>' . substr($device['location'], 0, 30) . '</td>' . PHP_EOL;
        $string .= '    <td nowrap>Uptime ' . formatUptime($device['uptime'], 'short') . '</td>' . PHP_EOL;
        $string .= '  </tr>' . PHP_EOL;
      }
    }
  }

  // Ports Down
  if ($status['ports'])
  {
    // FIXME Mike: This will be deleted in future
    // because $config['warn']['ifdown'] - deprecated.
    if (isset($config['warn']['ifdown']) && !$config['warn']['ifdown'])
    {
      $string .= "<tr><td colspan=6><h4>Please note that config option \$config['warn']['ifdown'] is now obsolete.</h4>";
      $string .= "<h4>Use options: \$config['frontpage']['device_status']['ports'] and \$config['frontpage']['device_status']['ports']</h4>";
      $string .= "For cancel this message, delete \$config['warn']['ifdown'] from configuration file.</td></tr>\n";
    }
  
    $query = 'SELECT * FROM `ports` AS I, `devices` AS D' . $query_perms;
    $query .= "WHERE I.device_id = D.device_id AND I.ifOperStatus = 'down' AND I.ifAdminStatus = 'up' AND I.ignore = 0 AND I.deleted = 0" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC, I.ifDescr * 1 ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $port)
    {
      $port = ifLabel($port, $port);
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="list-bold">' . generate_device_link($port, shorthost($port['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge badge-info">Port</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Port Down</span></td>' . PHP_EOL;
      $string .= '    <td class="list-bold">' . generate_port_link($port, makeshortif($port['label'])) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($port['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>Down for ' . formatUptime($config['time']['now'] - strtotime($port['ifLastChange']), 'short') . '</td>' . PHP_EOL; // This is like deviceUptime()
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  // Ports Errors (only deltas)
  if ($status['errors'])
  {
    $query = 'SELECT * FROM `ports` AS I, `ports-state` AS E, `devices` AS D' . $query_perms;
    $query .= "WHERE I.device_id = D.device_id AND I.ifOperStatus = 'up' AND I.ignore = 0 AND I.deleted = 0 AND I.port_id = E.port_id AND (E.ifInErrors_delta > 0 OR E.ifOutErrors_delta > 0)" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC, I.ifDescr * 1 ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $port)
    {
      $port = ifLabel($port, $port);
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="list-bold">' . generate_device_link($port, shorthost($port['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge badge-info">Port</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Port Errors</span></td>' . PHP_EOL;
      $string .= '    <td class="list-bold">'.generate_port_link($port, makeshortif($port['label']), 'port_errors') . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($port['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td>Errors ';
      if($port['ifInErrors_delta']) { $string .= 'In: ' . $port['ifInErrors_delta']; }
      if($port['ifInErrors_delta'] && $port['ifOutErrors_delta']) { $string .= ', '; }
      if($port['ifOutErrors_delta']) { $string .= 'Out: ' . $port['ifOutErrors_delta']; }
      $string .= '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  // Services
  if ($status['services'])
  {
    $query = 'SELECT * FROM `services` AS S, `devices` AS D' . $query_perms;
    $query .= "WHERE S.device_id = D.device_id AND S.service_status = 'down' AND S.service_ignore = 0" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $service)
    {
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="list-bold">' . generate_device_link($service, shorthost($service['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge">Service</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Service Down</span></td>' . PHP_EOL;
      $string .= '    <td>' . $service['service_type'] . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($service['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>Down for ' . formatUptime($config['time']['now'] - strtotime($service['service_changed']), 'short') . '</td>' . PHP_EOL; // This is like deviceUptime()
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  // BGP
  if ($status['bgp'])
  {
    if (isset($config['enable_bgp']) && $config['enable_bgp'])
    {
      // Description for BGP states
      $bgpstates = 'IDLE - Router is searching routing table to see whether a route exists to reach the neighbor. &#xA;';
      $bgpstates .= 'CONNECT - Router found a route to the neighbor and has completed the three-way TCP handshake. &#xA;';
      $bgpstates .= 'OPEN SENT - Open message sent, with parameters for the BGP session. &#xA;';
      $bgpstates .= 'OPEN CONFIRM - Router received agreement on the parameters for establishing session. &#xA;';
      $bgpstates .= 'ACTIVE - Router did not receive agreement on parameters of establishment. &#xA;';
      //$bgpstates .= 'ESTABLISHED - Peering is established; routing begins.';

      $query = 'SELECT * FROM `devices` AS D, bgpPeers AS B' . $query_perms;
      $query .= "WHERE bgpPeerAdminStatus = 'start' AND bgpPeerState != 'established' AND B.device_id = D.device_id" . $query_device . $query_user;
      $query .= 'ORDER BY D.hostname ASC';
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $peer)
      {
        $string .= '  <tr>' . PHP_EOL;
        $string .= '    <td class="list-bold">' . generate_device_link($peer, shorthost($peer['hostname'])) . '</td>' . PHP_EOL;
        $string .= '    <td><span class="badge badge-warning">BGP</span></td>' . PHP_EOL;
        $string .= '    <td><span class="label label-important" title="' . $bgpstates . '">BGP ' . strtoupper($peer['bgpPeerState']) . '</span></td>' . PHP_EOL;
        $string .= '    <td nowrap>' . $peer['bgpPeerIdentifier'] . '</td>' . PHP_EOL;
        $string .= '    <td nowrap>' . substr($peer['location'], 0, 30) . '</td>' . PHP_EOL;
        $string .= '    <td nowrap><strong>AS' . $peer['bgpPeerRemoteAs'] . ' :</strong> ' . substr($peer['astext'], 0, 15) . '</td>' . PHP_EOL;
        $string .= '  </tr>' . PHP_EOL;
      }
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';
  
  // Final print all statuses
  echo($string);
}

?>
