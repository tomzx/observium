<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.0.8
 *
 */

function print_vm_row($vm, $device = NULL)
{
  echo('<tr>');

  echo('<td>');

  if (getidbyname($vm['vmwVmDisplayName']))
  {
    echo(generate_device_link(device_by_name($vm['vmwVmDisplayName'])));
  } else {
    echo $vm['vmwVmDisplayName'];
  }

  echo("</td>");
  echo('<td>' . $vm['vmwVmState'] . "</td>");

  if ($vm['vmwVmGuestOS'] == "E: tools not installed")
  {
    echo('<td class="box-desc">Unknown (VMware Tools not installed)</td>');
  }
  else if ($vm['vmwVmGuestOS'] == "")
  {
    echo('<td class="box-desc"><i>(Unknown)</i></td>');
  }
  elseif (isset($config['vmware_guestid'][$vm['vmwVmGuestOS']]))
  {
    echo('<td>' . $config['vmware_guestid'][$vm['vmwVmGuestOS']] . "</td>");
  }
  else
  {
    echo('<td>' . $vm['vmwVmGuestOS'] . "</td>");
  }

  if ($vm['vmwVmMemSize'] >= 1024)
  {
    echo("<td class=list>" . sprintf("%.2f",$vm['vmwVmMemSize']/1024) . " GB</td>");
  } else {
    echo("<td class=list>" . sprintf("%.2f",$vm['vmwVmMemSize']) . " MB</td>");
  }

  echo('<td>' . $vm['vmwVmCpus'] . " CPU</td>");


}


/**
 * Generate Bootstrap-format Navbar
 *
 *   A little messy, but it works and lets us move to having no navbar markup on pages :)
 *   Examples:
 *   print_navbar(array('brand' => "Apps", 'class' => "navbar-narrow", 'options' => array('mysql' => array('text' => "MySQL", 'url' => generate_url($vars, 'app' => "mysql")))))
 *
 * @param array $vars
 * @return none
 *
 */

function print_navbar($navbar)
{
  global $config;

  $id = strgen();

  echo '<div class="navbar '.$navbar['class'].'">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target="#nav-'.$id.'">
          <span class="icon-bar"></span>
        </button>';

  if(isset($navbar['brand'])) { echo ' <a class="brand">'.$navbar['brand'].'</a>'; }
  echo('<div class="nav-collapse" id="nav-'.$id.'">');

  foreach(array('options', 'options_right') as $array_name)
  {
    if($array_name == "options_right") {
      echo('<ul class="nav pull-right">');
    } else {
      echo('<ul class="nav">');
    }
    foreach($navbar[$array_name] as $option => $array)
    {
      if($array[''] == "pull-right"){
        $navbar['options_right'][$option] = $array;
      } else {
        if(!is_array($array['suboptions']))
        {
          echo('<li class="'.$array['class'].'">');
          echo('<a href="'.$array['url'].'">');
          if(isset($array['icon'])) { echo('<i class="'.$array['icon'].'"></i> '); }
          echo($array['text'].'</a>');
          echo('</li>');
        } else {
          echo('  <li class="dropdown">');
          echo('    <a class="dropdown-toggle" data-toggle="dropdown"  href="'.$array['url'].'">');
          if(isset($array['icon'])) { echo('<i class="'.$array['icon'].'"></i> '); }
          echo($array['text'].'
            <b class="caret"></b>
          </a>
        <ul class="dropdown-menu">');
          foreach($array['suboptions'] as $suboption => $subarray)
          {
            echo('<li class="'.$subarray['class'].'">');
            echo('<a href="'.$subarray['url'].'">');
            if(isset($subarray['icon'])) { echo('<i class="'.$subarray['icon'].'"></i> '); }
            echo($subarray['text'].'</a>');
            echo('</li>');
          }
          echo('    </ul>
      </li>');
        }
      }
    }
    echo('</ul>');
  }

  echo '</div></div></div></div>';

}

/**
 * Generate search form (one line format)
 *
 * generates a simple search form.
 * Example of use:
 *  - array for 'select' item type
 *  $search[] = array('type'    => 'select',          // types allowed (select, text)
 *                    'name'    => 'Search By',       // Displayed name for item
 *                    'id'      => 'searchby',        // Item id
 *                    'width'   => '120px',           // (Optional) Item width
 *                    'value'   => $vars['searchby'], // (Optional) Current value for item
 *                    'values'  => array('mac' => 'MAC Address',
 *                                       'ip'  => 'IP Address'));  // Array with option items
 *  - array for 'text' item type (array keys same as above)
 *  $search[] = array('type'  => 'text',
 *                    'name'  => 'Address',
 *                    'id'    => 'address',
 *                    'width' => '120px',
 *                    'value' => $vars['address']);
 *  print_search_simple($search, 'Title here');
 *
 * @param array $data, string $title
 * @return none
 *
 */

function print_search_simple($data, $title = '')
{
  // Form header
  $string = PHP_EOL . '<!-- START search form -->' . PHP_EOL;
  $string .= '<div class="well well-shaded">' . PHP_EOL;
  $string .= '<form method="POST" action="" class="form form-inline">' . PHP_EOL;
  $string .= '  <table width="100%">' . PHP_EOL . '    <tr>' . PHP_EOL;
  $string .= '  <td><span style="font-weight: bold;">' . $title . '</span>&nbsp;&#187;</td>' . PHP_EOL;
  
  // Main
  $string .= '    <td>' . PHP_EOL;
  foreach($data as $item)
  {
    if (!isset($item['value'])) { $item['value'] = ''; }
    
    $string .= '  <div class="input-prepend" style="margin-right: 3px;">' . PHP_EOL;
    $string .= '    <span class="add-on">'.$item['name'].'</span>' . PHP_EOL;
    switch($item['type'])
    {
      case 'text':
        $string .= '    <input type="'.$item['type'].'" ';
        $string .= (isset($item['width'])) ? 'style="width:'.$item['width'].'" ' : '';
        $string .= 'name="'.$item['id'].'" id="'.$item['id'].'" class="input" value="'.$item['value'].'"/>' . PHP_EOL;
        break;
      case 'select':
        $string .= '    <select ';
        $string .= (isset($item['width'])) ? 'style="width:'.$item['width'].'" ' : '';
        $string .= 'name="'.$item['id'].'" id="'.$item['id'].'">' . PHP_EOL . '      ';
        foreach($item['values'] as $k => $v)
        {
          $string .= '<option value="'.$k.'"';
          $string .= ($k == $item['value']) ? ' selected>' : '>';
          $string .= $v.'</option> ';
        }
        $string .= PHP_EOL . '    </select>' . PHP_EOL;
        break;
    }
    $string .= '  </div>' . PHP_EOL;
  }
  $string .= '    </td>' . PHP_EOL;
  
  // Form footer
  $string .= '    <td align="center">' . PHP_EOL;
  $string .= '      <input type="hidden" name="pageno" value="1">' . PHP_EOL;
  $string .= '      <button type="submit" class="btn"><i class="icon-search"></i> Search</button>' . PHP_EOL;
  $string .= '    </td>' . PHP_EOL;
  $string .= '  </table>' . PHP_EOL . '    </tr>' . PHP_EOL;
  $string .= '</form>' . PHP_EOL . '</div>' . PHP_EOL;
  $string .= '<!-- END search form -->' . PHP_EOL . PHP_EOL;
  
  // Print search form
  echo($string);
}

/**
 * Display IPv4/IPv6 addresses.
 *
 * Display pages with IP addresses from device Interfaces.
 *
 * @param array $vars
 * @return none
 *
 */

function print_addresses($vars)
{
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
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

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `'.$address_type.'_addresses` AS A ';
  $query .= 'LEFT JOIN `ports`   AS I ON I.port_id   = A.port_id ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= 'LEFT JOIN `'.$address_type.'_networks` AS N ON N.'.$address_type.'_network_id = A.'.$address_type.'_network_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT('.$address_type.'_address_id) ' . $query;
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
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

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
      // If address not in specified network, don't show entry.
      if ($address_type === 'ipv4')
      {
        $address_show = Net_IPv4::ipInNetwork($entry[$address_type.'_address'], $addr . '/' . $mask);
      } else {
        $address_show = Net_IPv6::isInNetmask($entry[$address_type.'_address'], $addr, $mask);
      }
    }

    if ($address_show)
    {
      list($prefix, $length) = explode('/', $entry[$address_type.'_network']);

      if (port_permitted($entry['port_id']))
      {
        $entry = humanize_port ($entry, $entry);
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
  if ($pagination) { echo pagination($vars, $count); }

  // Print addresses
  echo $string;
}

/**
 * Display ARP/NDP table addresses.
 *
 * Display pages with ARP/NDP tables addresses from devices.
 *
 * @param array $vars
 * @return none
 *
 */

function print_arptable($vars)
{
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $address_search = FALSE;
  
  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
        case 'device_id':
          $where .= ' AND D.device_id = ?';
          $param[] = $value;
          break;
        case 'port':
        case 'port_id':
          $where .= ' AND I.port_id = ?';
          $param[] = $value;
          break;
        case 'ip_version':
          $where .= ' AND ip_version = ?';
          $param[] = $value;
          break;
        case 'address':
          $address_search = TRUE;
          if (isset($vars['searchby']) && $vars['searchby'] == 'ip')
          {
            $where .= ' AND `ip_address` LIKE ?';
            $value = trim($value);
            ///FIXME. Need another conversion ("2001:b08:b08" -> "2001:0b08:0b08") -- mike
            if (Net_IPv6::checkIPv6($value)) { $value = Net_IPv6::uncompress($value, true); }
            $param[] = '%'.$value.'%';
          } else {
            $where .= ' AND `mac_address` LIKE ?';
            $param[] = '%'.str_replace(array(':', ' ', '-', '.', '0x'),'',mres($value)).'%';
          }
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `ip_mac` AS M ';
  $query .= 'LEFT JOIN `ports`   AS I ON I.port_id   = M.port_id ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(mac_id) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY M.mac_address';
  if ($address_search) {
    $pagination = FALSE;
  } else {
    $query .= " LIMIT $start,$pagesize";
  }

  // Query ARP/NDP table addresses
  $entries = dbFetchRows($query, $param);
  // Query ARP/NDP table address count
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'search') { $list['device'] = TRUE; }
  if (!isset($vars['port']) || empty($vars['port']) || $vars['page'] == 'search') { $list['port'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed table-rounded">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>MAC Address</th>' . PHP_EOL;
    $string .= '      <th>IP Address</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['port']) { $string .= '      <th>Interface</th>' . PHP_EOL; }
    $string .= '      <th>Remote Device</th>' . PHP_EOL;
    $string .= '      <th>Remote Interface</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    if (port_permitted($entry['port_id']))
    {
      $entry = humanize_port ($entry, $entry);
      $ip_version = $entry['ip_version'];
      $ip_address = ($ip_version == 6) ? Net_IPv6::compress($entry['ip_address']) : $entry['ip_address'];
      $arp_host = dbFetchRow('SELECT * FROM ipv'.$ip_version.'_addresses AS A
                             LEFT JOIN ports AS I ON A.port_id = I.port_id
                             LEFT JOIN devices AS D ON D.device_id = I.device_id
                             WHERE A.ipv'.$ip_version.'_address = ?', array($ip_address));
      $arp_name = ($arp_host) ? generate_device_link($arp_host) : '';
      $arp_if = ($arp_host) ? generate_port_link($arp_host) : '';
      if ($arp_host['device_id'] == $entry['device_id']) { $arp_name = 'Self Device'; }
      if ($arp_host['port_id'] == $entry['port_id']) { $arp_if = 'Self Port'; }

      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td width="160">' . formatMac($entry['mac_address']) . '</td>' . PHP_EOL;
      $string .= '    <td width="140">' . $ip_address . '</td>' . PHP_EOL;
      if ($list['device'])
      {
        $string .= '    <td class="list-bold" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
      }
      if ($list['port'])
      { 
        if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
        {
          $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
        }
        $string .= '    <td class="list-bold">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
      }
      $string .= '    <td class="list-bold" width="200">' . $arp_name . '</td>' . PHP_EOL;
      $string .= '    <td class="list-bold">' . $arp_if . '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print ARP/NDP table
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
 */
function print_events($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']);
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
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
          $where .= ' AND E.device_id = ?';
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

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `eventlog` AS E ';
  $query .= 'LEFT JOIN `devices` AS D ON E.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(event_id) '.$query;
  /// FIXME Mike: bad table column `type` they intersect with table `devices`
  $query = 'SELECT STRAIGHT_JOIN E.device_id, E.timestamp, E.message, E.type, E.reference '.$query;
  $query .= ' ORDER BY `timestamp` DESC ';
  $query .= "LIMIT $start,$pagesize";

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
    $string .= format_timestamp($entry['timestamp']) . '</td>' . PHP_EOL;
    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['device_id']);
      $string .= '    <td class="list-bold">' . generate_device_link($dev, shorthost($dev['hostname'])) . '</td>' . PHP_EOL;
    }
    if ($list['port'])
    {
      if ($entry['type'] == 'interface')
      {
        $this_if = humanize_port(getifbyid($entry['reference']), $entry);
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
 */
function print_syslogs($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']);
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $priorities = syslog_priorities();

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

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `syslog` AS S ';
  $query .= 'LEFT JOIN `devices` AS D ON S.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_user . $query_device;
  $query_count = 'SELECT COUNT(seq) ' . $query;
  $query = 'SELECT STRAIGHT_JOIN * ' . $query;
  $query .= ' ORDER BY `timestamp` DESC ';
  $query .= "LIMIT $start,$pagesize";

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
      if (!$short) { $string .= '    <td style="color: ' . $priorities[$entry['priority']]['color'] . ';">(' . $entry['priority'] . ')&nbsp;' . $priorities[$entry['priority']]['name'] . '</td>' . PHP_EOL; }
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
  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  // Show Device Status
  if ($status['devices'])
  {
    $query = 'SELECT * FROM `devices` AS D ';
    $query .= $query_perms;
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
      $query = 'SELECT * FROM `devices` AS D ';
      $query .= $query_perms;
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
    /// FIXME Mike: This will be deleted in future
    /// because $config['warn']['ifdown'] - deprecated.
    if (isset($config['warn']['ifdown']) && !$config['warn']['ifdown'])
    {
  echo('
  <div class="alert">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
     <p><i class="fugue-bell"></i> <strong>Config option obsolete</strong></p>
     <p>Please note that config option <strong>$config[\'warn\'][\'ifdown\']</strong> is now obsolete.<br />Use options: <strong>$config[\'frontpage\'][\'device_status\'][\'ports\']</strong> and <strong>$config[\'frontpage\'][\'device_status\'][\'errors\']</strong></p>
     <p>To remove this message, delete <strong>$config[\'warn\'][\'ifdown\']</strong> from configuration file.</p>
  </div>');
    }

    $query = 'SELECT * FROM `ports` AS I ';
    $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE I.ifOperStatus = 'down' AND I.ifAdminStatus = 'up' AND I.ignore = 0 AND I.deleted = 0" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC, I.ifDescr * 1 ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $port)
    {
      $port = humanize_port($port, $port);
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
    $query = 'SELECT * FROM `ports` AS I ';
    $query .= 'LEFT JOIN `ports-state` AS E ON I.port_id = E.port_id ';
    $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE I.ifOperStatus = 'up' AND I.ignore = 0 AND I.deleted = 0 AND (E.ifInErrors_delta > 0 OR E.ifOutErrors_delta > 0)" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC, I.ifDescr * 1 ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $port)
    {
      $port = humanize_port($port, $port);
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
    $query = 'SELECT * FROM `services` AS S ';
    $query .= 'LEFT JOIN `devices` AS D ON S.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE S.service_status = 'down' AND S.service_ignore = 0" . $query_device . $query_user;
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

      $query = 'SELECT * FROM `devices` AS D ';
      $query .= 'LEFT JOIN bgpPeers AS B ON B.device_id = D.device_id ';
      $query .= $query_perms;
      $query .= "WHERE bgpPeerAdminStatus = 'start' AND bgpPeerState != 'established' " . $query_device . $query_user;
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
        $string .= '    <td><strong>AS' . $peer['bgpPeerRemoteAs'] . ' :</strong> ' . $peer['astext'] . '</td>' . PHP_EOL;
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
