<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

/**
 * Display Devices Inventory.
 *
 * @param array $vars
 * @return none
 *
 */
function print_inventory($vars)
{
  // On "Inventory" device tab display hierarchical list
  if ($vars['page'] == 'device' && is_numeric($vars['device']) && device_permitted($vars['device']))
  {
    echo('<table class="table table-striped table-bordered table-condensed table-rounded"><tr><td>');
    echo('<div class="btn-group pull-right" style="margin-top:3px;">
      <button class="btn" onClick=  "expandTree(\'enttree\');return false;"><i class="icon-plus"></i> Expand All Nodes</button>
      <button class="btn" onClick="collapseTree(\'enttree\');return false;"><i class="icon-minus"></i> Collapse All Nodes</button>
    </div>');

    echo("<div style='clear: both;'><ul class='mktree' id='enttree'>");
    $level = 0;
    $ent['entPhysicalIndex'] = 0;
    print_ent_physical($ent['entPhysicalIndex'], $level, "liOpen");
    echo('</ul></div>');
    echo('</td></tr></table>');
    return TRUE;
  }
  
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
        case 'device_id':
          $where .= ' AND E.device_id = ?';
          $param[] = $value;
          break;
        case 'parts':
          if (!is_array($value)) { $value = array($value); }
          $where .= ' AND (';
          foreach ($value as $v)
          {
            $where .= "E.entPhysicalModelName = ? OR ";
            $param[] = $v;
          }
          $where = substr($where, 0, -4) . ')';
          break;
        case 'serial':
          $where .= ' AND E.entPhysicalSerialNum LIKE ?';
          $param[] = '%'.$value.'%';
          break;
        case 'description':
          $where .= ' AND E.entPhysicalDescr LIKE ?';
          $param[] = '%'.$value.'%';
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
  if ($vars['page'] != 'device')
  {
    $query_device = ' AND D.ignore = 0 ';
    if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }
  }

  $query = 'FROM `entPhysical` AS E ';
  $query .= 'LEFT JOIN `devices` AS D ON D.device_id = E.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(*) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY D.`hostname`';
  $query .= " LIMIT $start,$pagesize";

  // Query inventories
  $entries = dbFetchRows($query, $param);
  // Query inventory count
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'inventory') { $list['device'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    $string .= '      <th>Name</th>' . PHP_EOL;
    $string .= '      <th>Description</th>' . PHP_EOL;
    $string .= '      <th>Part #</th>' . PHP_EOL;
    $string .= '      <th>Serial #</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $string .= '  <tr>' . PHP_EOL;
    if ($list['device'])
    {
      $string .= '    <td class="entity" nowrap>' . generate_device_link($entry, NULL, array('page' => 'device', 'tab' => 'entphysical')) . '</td>' . PHP_EOL;
    }
    $string .= '    <td width="160">' . $entry['entPhysicalName'] . '</td>' . PHP_EOL;
    $string .= '    <td>' . $entry['entPhysicalDescr'] . '</td>' . PHP_EOL;
    $string .= '    <td>' . $entry['entPhysicalModelName'] . '</td>' . PHP_EOL;
    $string .= '    <td>' . $entry['entPhysicalSerialNum'] . '</td>' . PHP_EOL;
    $string .= '  </tr>' . PHP_EOL;
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print Inventories
  echo $string;
}

/**
 * Display device inventory hierarchy.
 *
 * @param string $ent, $level, $class
 * @return none
 *
 */
function print_ent_physical($ent, $level, $class)
{
  global $device;

  $ents = dbFetchRows("SELECT * FROM `entPhysical` WHERE device_id = ? AND entPhysicalContainedIn = ? ORDER BY entPhysicalContainedIn,entPhysicalIndex", array($device['device_id'], $ent));
  foreach ($ents as $ent)
  {
    $link = '';
    $text = " <li class='$class'>";

    switch ($ent['entPhysicalClass'])
    {
      case 'chassis':
        $text .= '<i class="oicon-database"></i> ';
        break;
      case 'module':
        $text .= '<i class="oicon-drive"></i> ';
        break;
      case 'port':
        $text .= '<i class="oicon-network-ethernet"></i> ';
        break;
      case 'container':
        $text .= '<i class="oicon-box-zipper"></i> ';
        break;
      case 'stack':
        $text .= '<i class="oicon-databases"></i> ';
        break;
      case 'fan':
        $text .= '<i class="oicon-weather-wind"></i> ';
        break;
      case 'powerSupply':
        $text .= '<i class="oicon-plug"></i> ';
        break;
      case 'backplane':
        $text .= '<i class="oicon-zones"></i> ';
        break;
      case 'sensor':
        $text .= '<i class="oicon-asterisk"></i> ';
        $sensor = dbFetchRow("SELECT * FROM `sensors` AS S
                             LEFT JOIN `sensors-state` AS ST ON S.`sensor_id` = ST.`sensor_id`
                             WHERE `device_id` = ? AND (`entPhysicalIndex` = ? OR `sensor_index` = ?)", array($device['device_id'], $ent['entPhysicalIndex'], $ent['entPhysicalIndex']));
        break;
      default:
        $text .= '<i class="oicon-chain"></i> ';
    }

    if ($ent['entPhysicalParentRelPos'] > '-1') { $text .= '<strong>'.$ent['entPhysicalParentRelPos'].'.</strong> '; }

    $ent_text = '';

    if ($ent['ifIndex'])
    {
      $interface = dbFetchRow("SELECT * FROM `ports` WHERE ifIndex = ? AND device_id = ?", array($ent['ifIndex'], $device['device_id']));
      $ent['entPhysicalName'] = generate_port_link($interface);
    }

    if ($ent['entPhysicalModelName'] && $ent['entPhysicalName'])
    {
      $ent_text .= "<strong>".$ent['entPhysicalModelName']  . "</strong> (".$ent['entPhysicalName'].")";
    } elseif ($ent['entPhysicalModelName']) {
      $ent_text .= "<strong>".$ent['entPhysicalModelName']  . "</strong>";
    } elseif (is_numeric($ent['entPhysicalName']) && $ent['entPhysicalVendorType']) {
      $ent_text .= "<strong>".$ent['entPhysicalName']." ".$ent['entPhysicalVendorType']."</strong>";
    } elseif ($ent['entPhysicalName']) {
      $ent_text .= "<strong>".$ent['entPhysicalName']."</strong>";
    } elseif ($ent['entPhysicalDescr']) {
      $ent_text .= "<strong>".$ent['entPhysicalDescr']."</strong>";
    }

    $ent_text .= "<br /><div class='small' style='margin-left: 20px;'>" . $ent['entPhysicalDescr'];
    if ($ent['entPhysicalClass'] == "sensor" && $sensor['sensor_value'])
    {
      $ent_text .= ' ('.$sensor['sensor_value'] .' '. $sensor['sensor_class'].')';
      $link = generate_entity_link('sensor', $sensor, $ent_text);
    }

    $text .= ($link) ? $link : $ent_text;

    if ($ent['entPhysicalSerialNum'])
    {
      $text .= ' <span class="text-info">[Serial: '.$ent['entPhysicalSerialNum'].']</span> ';
    }

    $text .= "</div>";
    echo($text);

    $count = dbFetchCell("SELECT COUNT(*) FROM `entPhysical` WHERE device_id = '".$device['device_id']."' AND entPhysicalContainedIn = '".$ent['entPhysicalIndex']."'");
    if ($count)
    {
      echo("<ul>");
      print_ent_physical($ent['entPhysicalIndex'], $level+1, '');
      echo("</ul>");
    }
    echo("</li>");
  }
}

?>
