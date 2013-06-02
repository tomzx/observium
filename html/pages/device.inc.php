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


// Allow people to see this page if they have permission to see one of the ports, but don't show them tabs.

if ($vars['tab'] == "port" && is_numeric($vars['device']) && port_permitted($vars['port']))
{
  $check_device = get_device_id_by_port_id($vars['port']);
  $permit_ports = 1;
}

// Only show if they have access to the whole device or a single port.

if (device_permitted($vars['device']) || $check_device == $vars['device'])
{
  $selected['iface'] = "active";

  $tab = str_replace(".", "", mres($vars['tab']));

  if (!$tab)
  {
    $tab = "overview";
  }

  $select[$tab] = "active";

  $device  = device_by_id_cache($vars['device']);
  $attribs = get_dev_attribs($device['device_id']);
  $entity_state = get_dev_entity_state($device['device_id']);
  $device_state = unserialize($device['device_state']);

#  print_r($device_state);

#  print_r($entity_state);

  $pagetitle[] = $device['hostname'];

  if ($config['os'][$device['os']]['group']) { $device['os_group'] = $config['os'][$device['os']]['group']; }

  echo('<table class="table table-hover table-striped table-bordered table-condensed table-rounded" style="vertical-align: middle; margin-top: 5px; margin-bottom: 10px;">');
  #include("includes/hostbox.inc.php");
  include("includes/device-header.inc.php");

  echo('</table>');

  echo('<div class="tabbable">');

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

    if (@dbFetchCell("SELECT COUNT(app_id) FROM applications WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['apps'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'apps')).'">
      <i class="oicon-application-icon-large"></i> Apps
    </a>
  </li>');
    }

    if (isset($config['collectd_dir']) && is_dir($config['collectd_dir'] . "/" . $device['hostname'] ."/"))
    {
      echo('<li class="' . $select['collectd'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'collectd')).'">
      <i class="oicon-chart-up"></i> CollectD
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(mplug_id) FROM munin_plugins WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['munin'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'munin')). '">
      <i class="oicon-chart-up"></i> Munin
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(port_id) FROM ports WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['ports'] . $select['port'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'ports')). '">
      <i class="oicon-network-ethernet"></i> Ports
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(sla_id) FROM slas WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['slas'] . $select['sla'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'slas')). '">
      <i class="oicon-chart-up"></i> SLAs
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(accesspoint_id) FROM accesspoints WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['accesspoints'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'accesspoints')). '">
      <i class="oicon-wi-fi-zone"></i> Access Points
    </a>
  </li>');
    }

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

    if (count($smokeping_files['in'][$device['hostname']]) || count($smokeping_files['out'][$device['hostname']]))
    {
      echo('<li class="' . $select['latency'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'latency')).'">
      <i class="oicon-paper-plane"></i> Ping
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(vlan_id) FROM vlans WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['vlans'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'vlans')).'">
      <i class="oicon-arrow-branch-bgr"></i> VLANs
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(id) FROM vminfo WHERE device_id = '" . $device["device_id"] . "'") > '0')
    {
      echo('<li class="' . $select['vm'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'vm')).'">
      <i class="oicon-network-cloud"></i> Virtual Machines
    </a>
  </li>');
    }

    // $loadbalancer_tabs is used in device/loadbalancer/ to build the submenu. we do it here to save queries

    if ($device['os'] == "netscaler") // Netscaler
    {
      $device_loadbalancer_count['netscaler_vsvr'] = dbFetchCell("SELECT COUNT(*) FROM `netscaler_vservers` WHERE `device_id` = ?", array($device['device_id']));
      if ($device_loadbalancer_count['netscaler_vsvr']) { $loadbalancer_tabs[] = 'netscaler_vsvr'; }

      $device_loadbalancer_count['netscaler_services'] = dbFetchCell("SELECT COUNT(*) FROM `netscaler_services` WHERE `device_id` = ?", array($device['device_id']));
      if ($device_loadbalancer_count['netscaler_services']) { $loadbalancer_tabs[] = 'netscaler_services'; }
    }

    if ($device['os'] == "acsw")  // Cisco ACE
    {
      $device_loadbalancer_count['loadbalancer_vservers'] = dbFetchCell("SELECT COUNT(*) FROM `loadbalancer_vservers` WHERE `device_id` = ?", array($device['device_id']));
      if ($device_loadbalancer_count['loadbalancer_vservers']) { $loadbalancer_tabs[] = 'loadbalancer_vservers'; }
    }

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

    $device_routing_count['vrf'] = @dbFetchCell("SELECT COUNT(*) FROM `vrfs` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_routing_count['vrf']) { $routing_tabs[] = 'vrf'; }

    if (is_array($routing_tabs))
    {
      echo('<li class="' . $select['routing'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'routing')).'">
      <i class="oicon-arrow-branch-000-left"></i> Routing
    </a>
  </li>');
    }

    $device_pw_count = @dbFetchCell("SELECT COUNT(*) FROM `pseudowires` WHERE `device_id` = ?", array($device['device_id']));
    if ($device_pw_count)
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

    if (@dbFetchCell("SELECT COUNT(*) FROM `packages` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['packages'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'packages')).'">
      <i class="oicon-box-zipper"></i> Pkgs
    </a>
  </li>');
    }

    if ($config['enable_inventory'] && @dbFetchCell("SELECT * FROM `entPhysical` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['entphysical'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'entphysical')).'">
      <i class="menu-icon oicon-wooden-box"></i> Inventory
    </a>
  </li>');
    }
    elseif (device_permitted($device['device_id']) && $config['enable_inventory'] && @dbFetchCell("SELECT * FROM `hrDevice` WHERE device_id = '".$device['device_id']."'") > '0')
    {
      echo('<li class="' . $select['hrdevice'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'hrdevice')).'">
      <i class="menu-icon oicon-wooden-box"></i> Inventory
    </a>
  </li>');
    }

    if ($config['show_services'] && dbFetchCell("SELECT COUNT(service_id) FROM services WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['services'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'services')).'">
      <i class="oicon-target"></i> Services
    </a>
  </li>');
    }

    if (@dbFetchCell("SELECT COUNT(toner_id) FROM toner WHERE device_id = '" . $device['device_id'] . "'") > '0')
    {
      echo('<li class="' . $select['printing'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'printing')).'">
      <i class="oicon-printer-color"></i> Printing
    </a>
  </li>');
    }

    if (device_permitted($device['device_id']))
    {
      echo('<li class="' . $select['logs'] . '">
      <a href="'.generate_device_url($device, array('tab' => 'logs')).'">
        <i class="oicon-clipboard-audit"></i> Logs
      </a>
    </li>');
    }

    if (device_permitted($device['device_id']))
    {
      echo('<li class="' . $select['alerts'] . '">
      <a href="'.generate_device_url($device, array('tab' => 'alerts')).'">
        <i class="oicon-bell"></i> Alerts
      </a>
    </li>');
    }

    if ($_SESSION['userlevel'] >= 7)
    {
      $device_config_file = get_rancid_filename($device['hostname']);
    }

    if ($device_config_file)
    {
      echo('<li class="' . $select['showconfig'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'showconfig')).'/">
      <i class="oicon-application-terminal"></i> Config
    </a>
  </li>');
    }

    if ($config['nfsen_enable'])
    {
      $nfsen_rrd_file = get_nfsen_filename($device['hostname']);
    }

    if ($nfsen_rrd_file)
    {
      echo('<li class="' . $select['nfsen'] . '">
    <a href="'.generate_device_url($device, array('tab' => 'nfsen')).'">
      <i class="oicon-funnel"></i> Netflow
    </a>
  </li>');
    }

    if ($_SESSION['userlevel'] >= "7")
    {
      echo('<li class="' . $select['edit'] . ' pull-right" >
    <a href="'.generate_device_url($device, array('tab' => 'edit')).'">
      <i class="oicon-gear"></i>
    </a>
  </li>');

   echo('<li class="' . $select['perf'] . ' pull-right">
    <a href="'.generate_device_url($device, array('tab' => 'perf')).'">
      <i class="oicon-time"></i>
    </a>
  </li>');

    }
     echo("</ul>");
 }

  if (device_permitted($device['device_id']) || $check_device == $vars['device'])
  {
    echo('<div class="tab-content">');

    if (!$device['last_polled'])
    {
      echo('<div class="alert alert-info"><h3>Device not yet polled</h3><p>This device has not yet been successfully polled. System information and statistics will not be populated and graphs will not draw. Please wait 5-10 minutes for graphs to draw correctly.</p></div>');
    }

    include("pages/device/".mres(basename($tab)).".inc.php");

    echo("</div>");
  } else {
    include("includes/error-no-perm.inc.php");
  }
}

?>
