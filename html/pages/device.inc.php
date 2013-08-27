<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage webui
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

// If we've been given a hostname, try to retrieve the device_id

if (empty($vars['device']) and !empty($vars['hostname']))
{
  $vars['device'] = getidbyname($vars['hostname']);

  // If device lookup fails, generate an error.
  if (empty($vars['device']))
  {

    print_error('<h3>Invalid Hostname</h3>
                   A device matching the given hostname was not found. Please retype the hostname and try again.');

    break;
  }

}

// If there is no device specified in the URL, generate an error.
if (empty($vars['device']))
{

  print_error('<h3>No device specified</h3>
                   A valid device was not specified in the URL. Please retype and try again.');

  break;
}

// Allow people to see this page if they have permission to see one of the ports, but don't show them tabs.

if ($vars['tab'] == "port" && is_numeric($vars['device']) && port_permitted($vars['port']))
{
  $check_device = get_device_id_by_port_id($vars['port']);
  $permit_ports = 1;

  if($check_device != $vars['device'])
  {

  print_error('<h3>Invalid device/port combination</h3>
                   The port/device combination was invalid. Please retype and try again.');

  }

}

// Only show if the user has access to the whole device or a single port.

if (device_permitted($vars['device']) || $check_device == $vars['device'])
{
  $selected['iface'] = "active";

  $tab = str_replace(".", "", mres($vars['tab']));

  if (!$tab)
  {
    $tab = "overview";
  }

  $select[$tab] = "active";

  // Populate device array from pre-populated cache
  $device  = device_by_id_cache($vars['device']);

  // Populate the attributes array for this device
  $attribs = get_dev_attribs($device['device_id']);

  // Populate the entityPhysical state array for this device
  $entity_state = get_dev_entity_state($device['device_id']);

  // Populate the device state array from the serialized entry
  $device_state = unserialize($device['device_state']);

  // Add the device hostname to the page title array
  $pagetitle[] = $device['hostname'];

  // If the device's OS type has a group, set the device's os_group
  if ($config['os'][$device['os']]['group']) { $device['os_group'] = $config['os'][$device['os']]['group']; }

  // Print the device header inside a table.
  echo('<table class="table table-hover table-striped table-bordered table-condensed table-rounded" style="vertical-align: middle; margin-top: 5px; margin-bottom: 10px;">');
  include("includes/device-header.inc.php");
  echo('</table>');

  // Show tabs if the user has access to this device
  if (device_permitted($device['device_id']))
  {
  echo('<ul class="nav nav-tabs">');

   if ($config['show_overview_tab'])
    {
      echo('
  <li class="' . $select['overview'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'overview')).'">
      <i class="oicon-server"></i> Overview
    </a>
  </li>');
    }

    echo('<li class="' . $select['graphs'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'graphs')).'">
      <i class="oicon-chart-up"></i> Graphs
    </a>
  </li>');

    $health =  dbFetchCell("SELECT COUNT(*) FROM storage WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(sensor_id) FROM sensors WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(*) FROM cempMemPool WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(*) FROM cpmCPU WHERE device_id = '" . $device['device_id'] . "'") +
               dbFetchCell("SELECT COUNT(*) FROM processors WHERE device_id = '" . $device['device_id'] . "'");

    if ($health)
    {
      echo('<li class="' . $select['health'] . '">
      <a href="'.generate_device_url($device, array('tab' => 'health')).'">
        <i class="oicon-system-monitor"></i> Health
      </a>
    </li>');
    }

    // Print applications tab if there are matching entries in `applications` table
    if (dbFetchCell("SELECT COUNT(app_id) FROM applications WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['apps'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'apps')).'">
      <i class="oicon-application-icon-large"></i> Apps
    </a>
  </li>');
    }

    // Print the collectd tab if there is a matching directory
    if (isset($config['collectd_dir']) && is_dir($config['collectd_dir'] . "/" . $device['hostname'] ."/"))
    {
      echo('<li class="' . $select['collectd'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'collectd')).'">
      <i class="oicon-chart-up-color"></i> CollectD
    </a>
  </li>');
    }

    // Print the munin tab if there are matchng entries in the munin_plugins table
    if (dbFetchCell("SELECT COUNT(mplug_id) FROM munin_plugins WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['munin'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'munin')). '">
      <i class="oicon-chart-down-color"></i> Munin
    </a>
  </li>');
    }

    // Print the port tab if there are matching entries in the ports table
    if (dbFetchCell("SELECT COUNT(port_id) FROM ports WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['ports'] . $select['port'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'ports')). '">
      <i class="oicon-network-ethernet"></i> Ports
    </a>
  </li>');
    }

    // Print the SLAs tab if there are matching entries in the slas table
    if (dbFetchCell("SELECT COUNT(sla_id) FROM slas WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['slas'] . $select['sla'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'slas')). '">
      <i class="oicon-chart-up"></i> SLAs
    </a>
  </li>');
    }

    // Print the acceess points tab if there are matching entries in the accesspoints table
    if (dbFetchCell("SELECT COUNT(accesspoint_id) FROM accesspoints WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['accesspoints'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'accesspoints')). '">
      <i class="oicon-wi-fi-zone"></i> Access Points
    </a>
  </li>');
    }

    // Build array of smokeping files for use in tab building and smokeping page.
    if (isset($config['smokeping']['dir']))
    {
      $smokeping_files = array();
      if ($handle = opendir($config['smokeping']['dir']))
      {
        while (false !== ($file = readdir($handle)))
        {
          if ($file != "." && $file != "..")
          {
            if (strstr($file, ".rrd"))
            {
              if (strstr($file, "~"))
              {
                list($target,$slave) = explode("~", str_replace(".rrd", "", $file));
                $target = str_replace("_", ".", $target);
                $smokeping_files['in'][$target][$slave] = $file;
                $smokeping_files['out'][$slave][$target] = $file;
              } else {
                $target = str_replace(".rrd", "", $file);
                $target = str_replace("_", ".", $target);
                $smokeping_files['in'][$target][$config['own_hostname']] = $file;
                $smokeping_files['out'][$config['own_hostname']][$target] = $file;
              }
            }
          }
        }
      }
    }

    // Print latency tab if there are smokeping files with source or destination matching this hostname
    if (count($smokeping_files['in'][$device['hostname']]) || count($smokeping_files['out'][$device['hostname']]))
    {
      echo('<li class="' . $select['latency'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'latency')).'">
      <i class="oicon-paper-plane"></i> Ping
    </a>
  </li>');
    }

    // Print vlans tab if there are matching entries in the vlans table
    if (dbFetchCell("SELECT COUNT(vlan_id) FROM vlans WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['vlans'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'vlans')).'">
      <i class="oicon-arrow-branch-bgr"></i> VLANs
    </a>
  </li>');
    }

    // Pring Virtual Machines tab if there are matching entries in the vminfo table
    if (dbFetchCell("SELECT COUNT(id) FROM vminfo WHERE device_id = '" . $device["device_id"] . "'") > '0')
    {
      echo('<li class="' . $select['vm'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'vm')).'">
      <i class="oicon-network-cloud"></i> Virtual Machines
    </a>
  </li>');
    }

    // $loadbalancer_tabs is used in device/loadbalancer/ to build the submenu. we do it here to save queries

    // Check for Netscaler vservers and services
    if ($device['os'] == "netscaler") // Netscaler
    {
      $device_loadbalancer_count['netscaler_vsvr'] = dbFetchCell("SELECT COUNT(*) FROM `netscaler_vservers` WHERE `device_id` = ?", array($device['device_id']));
      if ($device_loadbalancer_count['netscaler_vsvr']) { $loadbalancer_tabs[] = 'netscaler_vsvr'; }

      $device_loadbalancer_count['netscaler_services'] = dbFetchCell("SELECT COUNT(*) FROM `netscaler_services` WHERE `device_id` = ?", array($device['device_id']));
      if ($device_loadbalancer_count['netscaler_services']) { $loadbalancer_tabs[] = 'netscaler_services'; }
    }

    // Check for Cisco ACE vservers
    if ($device['os'] == "acsw")  // Cisco ACE
    {
      $device_loadbalancer_count['loadbalancer_vservers'] = dbFetchCell("SELECT COUNT(*) FROM `loadbalancer_vservers` WHERE `device_id` = ?", array($device['device_id']));
      if ($device_loadbalancer_count['loadbalancer_vservers']) { $loadbalancer_tabs[] = 'loadbalancer_vservers'; }
    }

    // Print the load balancer tab if the loadbalancer_tabs array has entries.
    if (is_array($loadbalancer_tabs))
    {
      echo('<li class="' . $select['loadbalancer'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'loadbalancer')).'">
      <i class="oicon-arrow-split"></i> Load Balancer
    </a>
  </li>');
    }

    // $routing_tabs is used in device/routing/ to build the tabs menu. we built it here to save some queries

    $device_routing_count['loadbalancer_rservers'] = dbFetchCell("SELECT COUNT(*) FROM `loadbalancer_rservers` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['loadbalancer_rservers']) { $routing_tabs[] = 'loadbalancer_rservers'; }

    $device_routing_count['ipsec_tunnels'] = dbFetchCell("SELECT COUNT(*) FROM `ipsec_tunnels` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['ipsec_tunnels']) { $routing_tabs[] = 'ipsec_tunnels'; }

    $device_routing_count['bgp'] = dbFetchCell("SELECT COUNT(*) FROM `bgpPeers` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['bgp']) { $routing_tabs[] = 'bgp'; }

    $device_routing_count['ospf'] = dbFetchCell("SELECT COUNT(*) FROM `ospf_instances` WHERE `ospfAdminStat` = 'enabled' AND `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['ospf']) { $routing_tabs[] = 'ospf'; }

    $device_routing_count['eigrp'] = dbFetchCell("SELECT COUNT(*) FROM `eigrp_ports` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['eigrp']) { $routing_tabs[] = 'eigrp'; }

    $device_routing_count['cef'] = dbFetchCell("SELECT COUNT(*) FROM `cef_switching` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['cef']) { $routing_tabs[] = 'cef'; }

    $device_routing_count['vrf'] = dbFetchCell("SELECT COUNT(*) FROM `vrfs` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['vrf']) { $routing_tabs[] = 'vrf'; }

    // Print routing tab if any of the routing tables contain matching entries
    if (is_array($routing_tabs))
    {
      echo('<li class="' . $select['routing'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'routing')).'">
      <i class="oicon-arrow-branch-000-left"></i> Routing
    </a>
  </li>');
    }

    // Print the pseudowire tab if any of the routing tables contain matching entries
    if (dbFetchCell("SELECT COUNT(*) FROM `pseudowires` WHERE `device_id` = ?", array($device['device_id'])))
    {
      echo('<li class="' . $select['pseudowires'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'pseudowires')).'">
      <i class="oicon-layer-shape-curve"></i> Pseudowires
    </a>
  </li>');
    }

// moved to ports tab
#    if ($_SESSION['userlevel'] >= "5" && dbFetchCell("SELECT COUNT(*) FROM links AS L, ports AS I WHERE I.device_id = '".$device['device_id']."' AND I.port_id = L.local_port_id"))
#    {
#      $discovery_links = TRUE;
#      echo('<li class="' . $select['map'] . '">
#    <a href="'.generate_device_url($device, array('tab' => 'map')).'">
#      <i class="oicon-map"></i> Map
#    </a>
#  </li>');
#    }

    // Print the packages tab if there are matching entries in the packages table
    if (dbFetchCell("SELECT COUNT(*) FROM `packages` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['packages'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'packages')).'">
      <i class="oicon-box-zipper"></i> Pkgs
    </a>
  </li>');
    }

    // Print the inventory tab if inventory is enabled and either entphysical or hrdevice tables have entries
    if ($config['enable_inventory'] && dbFetchCell("SELECT * FROM `entPhysical` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['entphysical'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'entphysical')).'">
      <i class="menu-icon oicon-wooden-box"></i> Inventory
    </a>
  </li>');
    }
    elseif ($config['enable_inventory'] && dbFetchCell("SELECT * FROM `hrDevice` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['hrdevice'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'hrdevice')).'">
      <i class="menu-icon oicon-wooden-box"></i> Inventory
    </a>
  </li>');
    }

    // Print service tab if show_services enabled and there are entries in the services table
    ## DEPRECATED
    if ($config['show_services'] && dbFetchCell("SELECT COUNT(service_id) FROM services WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['services'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'services')).'">
      <i class="oicon-target"></i> Services
    </a>
  </li>');
    }

    // Print toner tab if there are entries in the toner table
    if (dbFetchCell("SELECT COUNT(toner_id) FROM toner WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['printing'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'printing')).'">
      <i class="oicon-printer-color"></i> Printing
    </a>
  </li>');
    }


    // Always print logs tab
    echo('<li class="' . $select['logs'] . '">
      <a href="'.generate_device_url($device, array('tab' => 'logs')).'">
        <i class="oicon-clipboard-audit"></i> Logs
      </a>
    </li>');

    // Always print alerts tab
    echo('<li class="' . $select['alerts'] . '">
      <a href="'.generate_device_url($device, array('tab' => 'alerts')).'">
        <i class="oicon-bell"></i> Alerts
      </a>
    </li>');

   // If the user has global read privileges, check for a device config. 
   if ($_SESSION['userlevel'] >= 7)
    {
      $device_config_file = get_rancid_filename($device['hostname']);
    }

    // Print the config tab if we have a device config
    if ($device_config_file)
    {
      echo('<li class="' . $select['showconfig'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'showconfig')).'/">
      <i class="oicon-application-terminal"></i> Config
    </a>
  </li>');
    }

    // If nfsen is enabled, check for an nfsen file
    if ($config['nfsen_enable'])
    {
      $nfsen_rrd_file = get_nfsen_filename($device['hostname']);
    }

    // Print the netflow tab if we have an nfsen file
    if ($nfsen_rrd_file)
    {
      echo('<li class="' . $select['nfsen'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'nfsen')).'">
      <i class="oicon-funnel"></i> Netflow
    </a>
  </li>');
    }

    // If the user has global write permissions, show them the edit tab
    if ($_SESSION['userlevel'] >= "10")
    {
      echo('<li class="' . $select['edit'] . ' pull-right" >
    <a href="'.generate_device_url($device, array('tab' => 'edit')).'">
      <i class="oicon-gear"></i>
    </a>
  </li>');

   // always print the performance tab
   echo('<li class="' . $select['perf'] . ' pull-right">
    <a href="'.generate_device_url($device, array('tab' => 'perf')).'">
      <i class="oicon-time"></i>
    </a>
  </li>');

    }
     echo("</ul>");
 }

  // Check that the user can view the device, or is viewing a permitted port on the device
  if (device_permitted($device['device_id']) || $check_device == $vars['device'])
  {

    // If this device has never been polled, print a warning here
    if (!$device['last_polled'] || $device['last_polled'] == '0000-00-00 00:00:00')
    {
      print_warning('<h3>Device not yet polled</h3>
This device has not yet been successfully polled. System information and statistics will not be populated and graphs will not draw.
Please wait 5-10 minutes for graphs to draw correctly.');
    }

    // If this device has never been discovered, print a warning here
    if (!$device['last_polled'] || $device['last_polled'] == '0000-00-00 00:00:00')
    {
      print_warning('<h3>Device not yet discovered</h3>
This device has not yet been successfully discovered. System information and statistics will not be populated and graphs will not draw.
This device should be automatically discovered within 10 minutes.');
    }

    if(is_file($config['html_dir']."/pages/device/".mres(basename($tab)).".inc.php"))
    {
      include($config['html_dir']."/pages/device/".mres(basename($tab)).".inc.php");
    } else {
           print_error('<h3>Tab does not exist</h3>
                          The requested tab does not exist. Please correct the URL and try again.');

    }

  } else {
    include("includes/error-no-perm.inc.php");
  }
}

?>
