<?php

// FIXME - this could do with some performance improvements, i think. possible rearranging some tables and setting flags at poller time (nothing changes outside of then anyways)

$service_alerts = dbFetchCell("SELECT COUNT(*) FROM services WHERE service_status = '0'");

?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target="#main-nav">
          <span class="oicon-bar"></span>
          <span class="oicon-bar"></span>
          <span class="oicon-bar"></span>
        </button>
        <a class="brand brand-observium" href="<?php generate_url(''); ?>">&nbsp;</a>
        <div class="nav-collapse" id="main-nav">
          <ul class="nav">
            <li class="divider-vertical" style="margin:0;"></li>
            <li class="dropdown">
              <a href="<?php echo(generate_url(array('page'=>'overview'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                <i class="oicon-globe-model"></i> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                <li><a href="<?php echo(generate_url(array('page'=>'overview'))); ?>"><i class="oicon-globe-model"></i> Overview</a></li>
                <li class="divider"></li>

<?php
// Custom navbar entries.
if(is_file("includes/navbar-custom.inc.php"))
{
 include("includes/navbar-custom.inc.php");

 echo('<li class="divider"></li>');
}

if (isset($config['enable_map']) && $config['enable_map'])
{
  echo('<li><a href="'.generate_url(array('page'=>'overview')).'"><span class="menu-icon oicon-map"></span> Network Map</a></li>');
}
?>
        <li><a href="<?php echo(generate_url(array('page'=>'eventlog'))); ?>"><i class="menu-icon oicon-clipboard-audit"></i> Eventlog</a></li>
<?php
if (isset($config['enable_syslog']) && $config['enable_syslog'])
{
          echo('<li><a href="'.generate_url(array('page'=>'syslog')).'"><i class="menu-icon oicon-clipboard-eye"></i> Syslog</a></li>');
}
?>
        <li><a href="<?php echo(generate_url(array('page'=>'pollerlog'))); ?>"><i class="menu-icon oicon-clipboard-report-bar"></i> Polling Information</a></li>
<!--        <li><a href="<?php echo(generate_url(array('page'=>'alerts'))); ?>"><i class="menu-icon oicon-bell"></i> Alerts</a></li> -->
        <li class="divider"></li>

        <li><a href="<?php echo(generate_url(array('page'=>'inventory'))); ?>"><i class="menu-icon oicon-wooden-box"></i> Inventory</a></li>

<?php

$packages = dbFetchCell("SELECT COUNT(*) from `packages`");

if ($packages)
{
  echo('<li><a href="'.generate_url(array('page'=>'packages')).'"><i class="oicon-box-zipper"></i> Software Packages</a></li>');
}

?>
          <li class="divider"></li>
          <li class="dropdown-submenu">
            <a tabindex="-1" href="<?php echo(generate_url(array('page'=>'search'))); ?>"><i class="menu-icon oicon-magnifier-zoom-actual"></i> Search</a>
            <ul class="dropdown-menu">
<?php
foreach (array('ipv4' => 'IPv4 Address', 'ipv6' => 'IPv6 Address', 'mac' => 'MAC Address', 'arp' => 'ARP/NDP Tables', 'fdb' => 'FDB Tables') as $search_page => $search_name)
{
  echo('            <li><a href="' . generate_url(array('page'=>'search','search'=>$search_page)) . '/"><i class="menu-icon  oicon-magnifier-zoom-actual"></i> ' . $search_name . ' </a></li>');
}
?>
            </ul>
          </li>

                </ul>
            </li>

            <li class="divider-vertical" style="margin:0;"></li>
            <li class="dropdown">
              <a href="<?php echo(generate_url(array('page'=>'devices'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="oicon-servers"></i> Devices <b class="caret"></b></a>
              <ul class="dropdown-menu" style="width:200px;">

                <li><a href="<?php echo(generate_url(array('page'=>'devices'))); ?>"><i class="oicon-servers"></i> All Devices</a></li>
                <li class="divider"></li>

<?php

if($config['location_menu_geocoded'])
{
?>
                 <li class="dropdown-submenu">
                    <a tabindex="-1" href="#"><i class="menu-icon oicon-building-hedge"></i> Locations</a>

<?php

function location_menu($array)
{
  global $config;

  ksort($array['entries']);

  echo('<ul style="" class="dropdown-menu" style="min-width: 250px;">');

  if(count($array['entries']) > "3")
  {
    foreach($array['entries'] as $entry => $entry_data)
    {
      if ($entry_data['level'] == "location_country")
      {
        $code = $entry;
        $entry = country_from_code($entry);
        $image = '<i class="flag flag-'.$code.'" alt="'.$entry.'"></i>';
      }
      elseif ($entry_data['level'] == "location")
      {
        echo('            <li><a href="' . generate_url(array('page'=>'devices','location'=> urlencode($entry))) . '/"><i class="menu-icon oicon-building"></i> ' . $entry . ' ('.$entry_data['count'].')</a></li>');
        continue;
      }

      echo('<li class="dropdown-submenu"><a href="' . generate_url(array('page'=>'devices',$entry_data['level'] => urlencode($entry))) . '/">
            '. $image .' ' . $entry . '('.$entry_data['count'].')</a>');

      location_menu($entry_data);
      echo('</li>');
    }
  } else {
    $new_entry_array = array();

    foreach($array['entries'] as $new_entry => $new_entry_data)
    {
      if ($new_entry_data['level'] == "location_country")
      {
        $code = $new_entry;
        $new_entry = country_from_code($new_entry);
        $image = '<i class="flag flag-'.$code.'" alt="'.$new_entry.'"></i> ';
      }
      elseif ($new_entry_data['level'] == "location")
      {
        echo('            <li><a href="' . generate_url(array('page'=>'devices','location'=> urlencode($new_entry))) . '/"><i class="menu-icon oicon-building"></i> ' . $new_entry . ' ('.$new_entry_data['count'].')</a></li>');
        continue;
      }

      echo('<li class="nav-header">'.$image.$new_entry.'</li>');
      foreach($new_entry_data['entries'] as $sub_entry => $sub_entry_data)
      {
        if(is_array($sub_entry_data['entries']))
        {
            echo('<li class="dropdown-submenu"><a style="" href="' . generate_url(array('page'=>'devices',$sub_entry_data['level']=> urlencode($sub_entry))) . '/">
                <i class="menu-icon oicon-building"></i> ' . $sub_entry . '('.$sub_entry_data['count'].')</a>');
          location_menu($sub_entry_data);
        } else {
          echo('            <li><a href="' . generate_url(array('page'=>'devices','location'=> urlencode($sub_entry))) . '/"><i class="menu-icon oicon-building"></i> ' . $sub_entry . ' ('.$sub_entry_data['count'].')</a></li>');
        }
        echo('</li>');
      }
    }
  }
  echo('</ul>');
}

location_menu($cache['locations']);

?>
                </li>
<?php
}

if($config['location_menu_geocoded'] == FALSE)
{
?>
                  <li class="dropdown-submenu">
                    <a tabindex="-1" href="#"><i class="menu-icon oicon-building"></i> Locations</a>
                    <ul class="dropdown-menu">


<?php
    foreach (getlocations() as $location)
    {
      if ($location != "")
      {
        echo('            <li><a href="' . generate_url(array('page'=>'devices','location'=> urlencode($location))) . '/"><i class="menu-icon oicon-building"></i> ' . $location . ' </a></li>');
      }
    }
?>
                  </ul>
                </li>
<?php
}

?>

                <li class="divider"></li>

<?php
foreach ($config['device_types'] as $devtype)
{
  if (in_array($devtype['type'],array_keys($cache['device_types'])))
  {
    echo('        <li><a href="devices/type=' . $devtype['type'] . '/"><i class="'.$devtype['icon'].'"></i> ' . $devtype['text'] . '&nbsp;<span class="right">('.$cache['device_types'][$devtype['type']].')</span></a></li>');
  }
}
?>
                <li class="divider"></li>
                <li><a href="addhost/"><i class="oicon-server--plus"></i> Add Device</a></li>
                <li><a href="delhost/"><i class="oicon-server--minus"></i> Delete Device</a></li>
              </ul>
            </li>

            <li class="divider-vertical" style="margin:0;"></li>

            <li class="dropdown">
              <a href="<?php echo(generate_url(array('page'=>'ports'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="oicon-network-ethernet"></i> Ports <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo(generate_url(array('page'=>'ports'))); ?>"><i class="oicon-network-ethernet"></i> All Ports&nbsp;<span class="right">(<?php echo($ports['count']); ?>)</span></a></li>
                <li class="divider"></li>

<?php

if ($config['enable_billing']) { echo('<li><a href="bills/"><i class="oicon-money-coin"></i> Traffic Bills</a></li>'); $ifbreak = 1; }

if ($config['enable_pseudowires']) { echo('<li><a href="pseudowires/"><i class="oicon-layer-shape-curve"></i> Pseudowires</a></li>'); $ifbreak = 1; }

?>

<?php

if ($_SESSION['userlevel'] >= '5')
{
  // FIXME new icons
  echo('<li class="divider"></li>');
  if ($config['int_customers']) { echo('<li><a href="customers/"><img src="images/16/group_link.png" border="0" align="absmiddle" /> Customers</a></li>'); $ifbreak = 1; }
  if ($config['int_l2tp']) { echo('<li><a href="iftype/type=l2tp/"><img src="images/16/user.png" border="0" align="absmiddle" /> L2TP</a></li>'); $ifbreak = 1; }
  if ($config['int_transit']) { echo('<li><a href="iftype/type=transit/"><img src="images/16/lorry_link.png" border="0" align="absmiddle" /> Transit</a></li>');  $ifbreak = 1; }
  if ($config['int_peering']) { echo('<li><a href="iftype/type=peering/"><img src="images/16/bug_link.png" border="0" align="absmiddle" /> Peering</a></li>'); $ifbreak = 1; }
  if ($config['int_peering'] && $config['int_transit']) { echo('<li><a href="iftype/type=peering,transit/"><img src="images/16/world_link.png" border="0" align="absmiddle" /> Peering & Transit</a></li>'); $ifbreak = 1; }
  if ($config['int_core']) { echo('<li><a href="iftype/type=core/"><img src="images/16/brick_link.png" border="0" align="absmiddle" /> Core</a></li>'); $ifbreak = 1; }
  // Custom interface groups can be set - see Interface Description Parsing
  foreach ($config['int_groups'] as $int_type)
  {
         echo('<li><a href="iftype/type=' . $int_type . '/"><img src="images/16/brick_link.png" border="0" align="absmiddle" /> ' . $int_type .'</a></li>'); $ifbreak = 1;
  }
}

if ($ifbreak) { echo('<li class="divider"></li>'); }

/// FIXME. Make Down/Ignored/Disabled ports as submenu. --mike
if (isset($ports['alerts']))
{
  echo('<li><a href="ports/alerted=yes/"><img src="images/16/link_error.png" border="0" align="absmiddle" /> Alerts&nbsp;<span class="right">('.$ports['alerts'].')</span></a></li>');
}

if ($ports['errored'])
{
  echo('<li><a href="ports/errors=yes/"><img src="images/16/chart_curve_error.png" border="0" align="absmiddle" /> Errored&nbsp;<span class="right">('.$ports['errored'].')</span></a></li>');
}

if ($ports['down'])
{
  echo('<li><a href="ports/state=down/"><i class="oicon-network-status-busy"></i> Down</a></li>'); // &nbsp;<span class="right">('.$ports['down'].')</span></a></li>');
}

if ($ports['ignored'])
{
  echo('<li><a href="ports/ignore=1/"><img src="images/16/chart_curve_link.png" border="0" align="absmiddle" /> Ignored</a></li>'); // &nbsp;<span class="right">('.$ports['ignored'].')</span></a></li>');
}

if ($ports['disabled'])
{
  echo('<li><a href="ports/state=admindown/"><i class="oicon-network-status-offline"></i> Disabled</a></li>'); // &nbsp;<span class="right">('.$ports['disabled'].')</span></a></li>');
}

if ($ports['deleted']) { echo('<li><a href="deleted-ports/"><i class="oicon-badge-square-minus"></i> Deleted&nbsp;<span class="right">('.$ports['deleted'].')</span></a></li>'); }
?>

              </ul>
            </li>
<?php

// FIXME does not check user permissions...
foreach (dbFetchRows("SELECT sensor_class,COUNT(sensor_id) AS c FROM sensors GROUP BY sensor_class ORDER BY sensor_class ") as $row)
{
  $used_sensors[$row['sensor_class']] = $row['c'];
  #$config['sensor_types']['current']
}

// Copy the variable so we can use $used_sensors later in other parts of the code
$menu_sensors = $used_sensors;

// This stuff is all very complex. It needs simplified into one or two loops, perhaps a function.

?>
            <li class="divider-vertical" style="margin:0;"></li>
            <li class="dropdown">
              <a href="<?php echo(generate_url(array('page'=>'ports'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="oicon-system-monitor"></i> Health <b class="caret"></b></a>
              <ul class="dropdown-menu">

<?php
$items = array('mempool' => array('text' => "Memory", 'icon' => 'oicon-memory'),
               'processor' => array('text' => "Processors", 'icon' => 'oicon-processor'),
               'storage' => array('text' => "Storage", 'icon' => 'oicon-drive'));

foreach ($items as $item => $item_data)
{
  echo('<li><a href="'.generate_url(array('page'=>'health', 'metric' => $item)).'"><i class="'.$item_data['icon'].'"></i> '.$item_data['text'].'</a></li>');
  unset($menu_sensors[$item]);$sep++;
}
?>

<?php
if ($menu_sensors)
{
  $sep = 0;
  echo('<li class="divider"></li>');
}

foreach (array('fanspeed','humidity','temperature') as $item)
{
  if ($menu_sensors[$item])
  {
    echo('<li><a href="'.generate_url(array('page'=>'health', 'metric' => $item)).'"><i class="'.$config['sensor_types'][$item]['icon'].'"></i> '.nicecase($item).'</a></li>');
    unset($menu_sensors[$item]);$sep++;
  }
}

if ($sep)
{
  echo('<li class="divider"></li>');
  $sep = 0;
}

foreach (array('current','frequency','power','voltage') as $item)
{
  if ($menu_sensors[$item])
  {
    echo('<li><a href="'.generate_url(array('page'=>'health', 'metric' => $item)).'"><i class="'.$config['sensor_types'][$item]['icon'].'"></i> '.nicecase($item).'</a></li>');
    unset($menu_sensors[$item]);$sep++;
  }
}

if ($sep && array_keys($menu_sensors))
{
  echo('<li class="divider"></li>');
  $sep = 0;
}

foreach (array_keys($menu_sensors) as $item)
{
  echo('<li><a href="'.generate_url(array('page'=>'health', 'metric' => $item)).'"><i class="'.$config['sensor_types'][$item]['icon'].'"></i> '.nicecase($item).'</a></li>');
  unset($menu_sensors[$item]);$sep++;
}

?>
              </ul>
            </li>

<?php

$app_count = dbFetchCell("SELECT COUNT(`app_id`) FROM `applications`");

if ($_SESSION['userlevel'] >= '5' && ($app_count) > "0")
{
?>
            <li class="divider-vertical" style="margin:0;"></li>
    <li class="dropdown">
      <a href="<?php echo(generate_url(array('page'=>'apps'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="oicon-application-icon-large"></i> Apps <b class="caret"></b></a>
      <ul class="dropdown-menu">
<?php

  $app_list = dbFetchRows("SELECT `app_type` FROM `applications` GROUP BY `app_type` ORDER BY `app_type`");
  foreach ($app_list as $app)
  {
    $image = $config['html_dir']."/images/icons/".$app['app_type'].".png";
    $icon = (file_exists($image) ? $app['app_type'] : "apps");
    //$icon = $image;
    echo('<li><a href="apps/app='.$app['app_type'].'/"><img src="images/icons/'.$icon.'.png" border="0" align="absmiddle" /> '.nicecase($app['app_type']).' </a></li>');
  }

?>

              </ul>
            </li>

<?php
}

if ($_SESSION['userlevel'] >= '5' && ($routing['bgp']['count']+$routing['ospf']['count']+$routing['cef']['count']+$routing['vrf']['count']) > 0)
{
?>
     <li class="divider-vertical" style="margin:0;"></li>
     <li class="dropdown">
       <a href="<?php echo(generate_url(array('page'=>'routing'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="oicon-arrow-branch-000-left"></i> Routing <b class="caret"></b></a>
       <ul class="dropdown-menu" style="width:200px;">

<?php
  $separator = 0;

  if ($_SESSION['userlevel'] >= '5' && $routing['vrf']['count'])
  {
    echo('<li><a href="routing/protocol=vrf/"><i class="oicon-arrow-branch-byr"></i> VRFs&nbsp;<span class="right">(' . $routing['vrf']['count'] . ')</span></a></li>');
    $separator++;
  }

  if ($_SESSION['userlevel'] >= '5' && $routing['ospf']['count'])
  {
    if ($separator)
    {
      echo('<li class="divider"></li>');
      $separator = 0;
    }
    echo('
        <li><a href="routing/protocol=ospf/"><img src="images/16/text_letter_omega.png" border="0" align="absmiddle" /> OSPF Instances&nbsp;<span class="right">(' . $routing['ospf']['count'] . ')</span></a></li>');
    $separator++;
  }
  // BGP Sessions
  if ($_SESSION['userlevel'] >= '5' && $routing['bgp']['count'])
  {
    if ($separator)
    {
      echo('<li class="divider"></li>');
      $separator = 0;
    }
    echo('
        <li><a href="routing/protocol=bgp/type=all/graph=NULL/"><img src="images/16/link.png" border="0" align="absmiddle" /> BGP All Sessions&nbsp;<span class="right">(' . $routing['bgp']['count'] . ')</span></a></li>

        <li><a href="routing/protocol=bgp/type=external/graph=NULL/"><img src="images/16/world_link.png" border="0" align="absmiddle" /> BGP External</a></li>
        <li><a href="routing/protocol=bgp/type=internal/graph=NULL/"><img src="images/16/brick_link.png" border="0" align="absmiddle" /> BGP Internal</a></li>');
  }

  // Do Alerts at the bottom
  if ($routing['bgp']['alerts'])
  {
    echo('
        <li class="divider"></li>
        <li><a href="routing/protocol=bgp/adminstatus=start/state=down/"><img src="images/16/link_error.png" border="0" align="absmiddle" /> BGP Alerts&nbsp;<span class="right">(' . $routing['bgp']['alerts'] . ')</span></a></li>
   ');
  }

  echo('      </ul></li>');

}
?>
</ul>
          <ul class="nav pull-right">

          <li class="dropdown">
            <form id="searchform" class="navbar-search" action="" style="margin-left: 10px; margin-right: 10px;  margin-top: 5px; margin-bottom: -5px;">
              <input onkeyup="lookup(this.value);" type="text" value="" class="span2 dropdown-toggle" placeholder="Search" />
            </form>
            <div id="suggestions" class="typeahead dropdown-menu"></div>
          </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="oicon-gear"></i> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="http://www.observium.org/wiki/Documentation" title="Help"><i class="oicon-question"></i> Help</a></li>
                <li class="divider"></li>

<?php

if($_SESSION['widescreen'] == 1)
{
  echo('<li><a href="'.generate_url($vars, array('widescreen' => 'no')).'" title="Switch to normal screen width layout"><i class="oicon-arrow-in" style="font-size: 16px; color: #555;"></i> Normal Width</a></li>');
} else {
  echo('<li><a href="'.generate_url($vars, array('widescreen' => 'yes')).'" title="Switch to wide screen layout"><i class="oicon-arrow-move" style="font-size: 16px; color: #555;"></i> Widescreen</a></li>');
}

if($_SESSION['big_graphs'] == 1)
{
  echo('<li><a href="'.generate_url($vars, array('big_graphs' => 'no')).'" title="Switch to normal graphs"><i class="oicon-layout-6" style="font-size: 16px; color: #555;"></i> Normal Graphs</a></li>');
} else {
  echo('<li><a href="'.generate_url($vars, array('big_graphs' => 'yes')).'" title="Switch to larger graphs"><i class="oicon-layout-4" style="font-size: 16px; color: #555;"></i> Large Graphs</a></li>');
}

if ($config['api']['enabled'])
{
  echo('<li class="divider"></li>');
  echo('<li class="dropdown-submenu">');
  echo('  <a tabindex="-1" href="'.generate_url(array('page'=>'simpleapi')).'"><i class="oicon-application-block"></i> Simple API</a>');
  echo('  <ul class="dropdown-menu">');
  echo('    <li><a href="'.generate_url(array('page'=>'simpleapi')).'"><i class="oicon-application-block"></i> API Manual</a></li>');
  echo('    <li><a href="'.generate_url(array('page'=>'simpleapi','api'=>'errorcodes')).'"><i class="oicon-application--exclamation"></i> Error Codes</a></li>');
  echo('  </ul>');
  echo('</li>');
}

if ($_SESSION['userlevel'] >= 10)
{
  echo('<li class="divider"></li>');
  echo('<li class="dropdown-submenu">');
  echo('  <a tabindex="-1" href="'.generate_url(array('page'=>'adduser')).'"><i class="oicon-users"></i> Users</a>');
  echo('  <ul class="dropdown-menu">');
  echo('    <li><a href="'.generate_url(array('page'=>'adduser')).'"><i class="oicon-user--plus"></i> Add User</a></li>');
  echo('    <li><a href="'.generate_url(array('page'=>'edituser')).'"><i class="oicon-user--pencil"></i> Edit User</a></li>');
  echo('    <li><a href="'.generate_url(array('page'=>'edituser')).'"><i class="oicon-user--minus"></i> Remove User</a></li>');
  echo('    <li><a href="'.generate_url(array('page'=>'authlog')).'"><i class="oicon-user-detective"></i> Authentication Log</a></li>');
  echo('  </ul>');
  echo('</li>');
}
?>
                <li class="divider"></li>
                <li><a href="<?php echo generate_url(array('page'=>'settings')); ?>" title="Global Settings"><i class="oicon-wrench"></i> Global Settings</a></li>
                <li><a href="<?php echo generate_url(array('page'=>'preferences')); ?>" title="My Settings "><i class="oicon-wrench-screwdriver"></i> My Settings</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo generate_url(array('page'=>'logout')); ?>" title="Logout"><i class="oicon-door-open-out"></i> Logout</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo generate_url(array('page'=>'about')); ?>" title="About Observium"><i class="oicon-information"></i> About Observium</a></li>
              </ul>
            </li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div>
    </div><!-- /navbar-inner -->
  </div>

<?php
if($_SESSION['widescreen'] == 1)
{
  echo('
  <div class="alert">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
     <p><i class="oicon-arrow-move"></i> <strong>Widescreen Mode</strong></p>
     <p>Please note that widescreen mode is currently not working, but will return once it has been ported to Bootstrap properly.</p>
     <p><a class="btn btn" href="'.generate_url($vars, array('widescreen' => 'no')).'"><i class="oicon-arrow-in"></i> Return to normal</a></p>
  </div>');

}
?>

<script>

function lookup(inputString) {
   if (inputString.length == 0) {
      $('#suggestions').fadeOut(); // Hide the suggestions box
   } else {
      $.post("ajax_search.php", {queryString: ""+inputString+""}, function(data) { // Do an AJAX call
         $('#suggestions').fadeIn(); // Show the suggestions box
         $('#suggestions').html(data); // Fill the suggestions box
      });
   }
}

</script>
