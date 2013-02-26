<?php

// FIXME - this could do with some performance improvements, i think. possible rearranging some tables and setting flags at poller time (nothing changes outside of then anyways)

$service_alerts = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_status = '0'");
$if_alerts      = dbFetchCell("SELECT COUNT(port_id) FROM `ports` WHERE `ifOperStatus` = 'down' AND `ifAdminStatus` = 'up' AND `ignore` = '0'");

if (isset($config['enable_bgp']) && $config['enable_bgp'])
{
  $bgp_alerts = dbFetchCell("SELECT COUNT(bgpPeer_id) FROM bgpPeers AS B where (bgpPeerAdminStatus = 'start' OR bgpPeerAdminStatus = 'running') AND bgpPeerState != 'established'");
}


// Custom menubar entries.
#if(is_file("includes/print-menubar-custom.inc.php"))
#{
#  include("includes/print-menubar-custom.inc.php");
#}

?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="brand" href="<?php generate_url(); ?>"><img src="images/observium-mini-logo.png" /></a>
        <div class="nav-collapse">
          <ul class="nav">
            <li class="divider-vertical" style="margin:0;"></li>
            <li class="dropdown">
              <a href="<?php echo(generate_url(array('page'=>'overview'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
                <i class="fugue-globe-model"></i> Overview <b class="caret"></b></a>
                <ul class="dropdown-menu">
                <li><a href="<?php echo(generate_url(array('page'=>'overview'))); ?>"><i class="fugue-globe-model"></i> Overview</a></li>
                <li class="divider"></li>

        <?php if (isset($config['enable_map']) && $config['enable_map']) {
          echo('<li><a href="'.generate_url(array('page'=>'overview')).'"><span class="menu-icon icon-map"></span> Network Map</a></li>');
        } ?>

        <li><a href="<?php echo(generate_url(array('page'=>'eventlog'))); ?>"><i class="menu-icon sweetie-clipboard-audit"></i> Eventlog</a></li>
        <?php if (isset($config['enable_syslog']) && $config['enable_syslog']) {
          echo('<li><a href="'.generate_url(array('page'=>'syslog')).'"><i class="menu-icon sweetie-clipboard-eye"></i> Syslog</a></li>');
        } ?>
        <li><a href="<?php echo(generate_url(array('page'=>'alerts'))); ?>"><i class="menu-icon fugue-bell"></i> Alerts</a></li>
        <li><a href="<?php echo(generate_url(array('page'=>'inventory'))); ?>"><i class="menu-icon fugue-wooden-box"></i> Inventory</a></li>

<?php

$packages = dbFetchCell("SELECT COUNT(pkg_id) from `packages`");

if ($packages)
{
        echo('<li><a href="'.generate_url(array('page'=>'packages')).'"><i class="fugue-box-zipper"></i> All Packages</a></li>');
}

?>

          <li class="divider"></li>
          <li><a href="<?php echo(generate_url(array('page'=>'search','search'=>'ipv4'))); ?>"><i class="menu-icon fugue-magnifier-zoom-actual"></i> Search</a></li>

                </ul>
            </li>


            <li class="divider-vertical" style="margin:0;"></li>
            <li class="dropdown">
              <a href="<?php echo(generate_url(array('page'=>'devices'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="fugue-servers"></i> Devices <b class="caret"></b></a>
              <ul class="dropdown-menu">

                <li><a href="<?php echo(generate_url(array('page'=>'devices'))); ?>"><i class="fugue-servers"></i> All Devices</a></li>
                <li class="divider"></li>


                  <li class="dropdown-submenu">
                    <a tabindex="-1" href="#"><i class="menu-icon fugue-building"></i> Locations</a>
                    <ul class="dropdown-menu">


<?php
    foreach (getlocations() as $location)
    {
      echo('            <li><a href="' . generate_url(array('page'=>'devices','location'=> urlencode($location))) . '/"><i class="menu-icon fugue-building"></i> ' . $location . ' </a></li>');
    }
?>
                  </ul>
                </li>

                <li class="divider"></li>



<?php
foreach ($config['device_types'] as $devtype)
{
#  if (in_array($devtype['type'],array_keys($cache['device_types'])))
#  {
    echo('        <li><a href="devices/type=' . $devtype['type'] . '/"><i class="'.$devtype['icon'].'"></i> ' . $devtype['text'] . '</a></li>');
#  }
}
?>
                <li class="divider"></li>
                <li><a href="addhost/"><i class="fugue-server--plus"></i> Add Device</a></li>
                <li><a href="delhost/"><i class="fugue-server--minus"></i> Delete Device</a></li>
              </ul>
            </li>

            <li class="divider-vertical" style="margin:0;"></li>

            <li class="dropdown">
              <a href="<?php echo(generate_url(array('page'=>'ports'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="fugue-network-ethernet"></i> Ports <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo(generate_url(array('page'=>'ports'))); ?>"><i class="fugue-network-ethernet"></i> All Ports</b></a></li>
                <li class="divider"></li>


<?php

if ($ports['errored'])
{
  echo('<li><a href="ports/errors=1/"><img src="images/16/chart_curve_error.png" border="0" align="absmiddle" /> Errored ('.$ports['errored'].')</a></li>');
}

if ($ports['ignored'])
{
  echo('<li><a href="ports/ignore=1/"><img src="images/16/chart_curve_link.png" border="0" align="absmiddle" /> Ignored ('.$ports['ignored'].')</a></li>');
}

if ($config['enable_billing']) { echo('<li><a href="bills/"><i class="fugue-money-coin"></i> Traffic Bills</a></li>'); $ifbreak = 1; }

if ($config['enable_pseudowires']) { echo('<li><a href="pseudowires/"><i class="fugue-layer-shape-curve"></i> Pseudowires</a></li>'); $ifbreak = 1; }

?>

<?php

if ($_SESSION['userlevel'] >= '5')
{
  // fixme new icons
  echo('<li class="divider"></li>');
  if ($config['int_customers']) { echo('<li><a href="customers/"><img src="images/16/group_link.png" border="0" align="absmiddle" /> Customers</a></li>'); $ifbreak = 1; }
  if ($config['int_l2tp']) { echo('<li><a href="iftype/type=l2tp/"><img src="images/16/user.png" border="0" align="absmiddle" /> L2TP</a></li>'); $ifbreak = 1; }
  if ($config['int_transit']) { echo('<li><a href="iftype/type=transit/"><img src="images/16/lorry_link.png" border="0" align="absmiddle" /> Transit</a></li>');  $ifbreak = 1; }
  if ($config['int_peering']) { echo('<li><a href="iftype/type=peering/"><img src="images/16/bug_link.png" border="0" align="absmiddle" /> Peering</a></li>'); $ifbreak = 1; }
  if ($config['int_peering'] && $config['int_transit']) { echo('<li><a href="iftype/type=peering,transit/"><img src="images/16/world_link.png" border="0" align="absmiddle" /> Peering & Transit</a></li>'); $ifbreak = 1; }
  if ($config['int_core']) { echo('<li><a href="iftype/type=core/"><img src="images/16/brick_link.png" border="0" align="absmiddle" /> Core</a></li>'); $ifbreak = 1; }
}

if ($ifbreak) { echo('<li class="divider"></li>'); }

if (isset($interface_alerts))
{
  echo('<li><a href="ports/alerted=yes/"><img src="images/16/link_error.png" border="0" align="absmiddle" /> Alerts ('.$interface_alerts.')</a></li>');
}

$deleted_ports = 0;
foreach (dbFetchRows("SELECT * FROM `ports` AS P, `devices` as D WHERE P.`deleted` = '1' AND D.device_id = P.device_id") as $interface)
{
  if (port_permitted($interface['port_id'], $interface['device_id']))
  {
    $deleted_ports++;
  }
}
?>

<li><a href="ports/state=down/"><i class="fugue-network-status-busy"></i> Down</a></li>
<li><a href="ports/state=admindown/"><i class="fugue-network-status-offline"></i> Disabled</a></li>
<?php
if ($deleted_ports) { echo('<li><a href="deleted-ports/"><i class="sweetie-badge-square-minus"></i> Deleted ('.$deleted_ports.')</a></li>'); }
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
              <a href="<?php echo(generate_url(array('page'=>'ports'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="fugue-system-monitor"></i> Health <b class="caret"></b></a>
              <ul class="dropdown-menu">

<?php
$items = array('mempools' => array('text' => "Memory", 'icon' => 'fugue-memory'),
               'processor' => array('text' => "Processors", 'icon' => 'fugue-processor'),
               'storage' => array('text' => "Storage", 'icon' => 'fugue-drive'));

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
      <a href="<?php echo(generate_url(array('page'=>'apps'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="fugue-application-icon-large"></i> Apps <b class="caret"></b></a>
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

$routing_count['bgp']  = dbFetchCell("SELECT COUNT(bgpPeer_id) from `bgpPeers`");
$routing_count['ospf'] = dbFetchCell("SELECT COUNT(ospf_instance_id) FROM `ospf_instances` WHERE `ospfAdminStat` = 'enabled'");
$routing_count['cef']  = dbFetchCell("SELECT COUNT(cef_switching_id) from `cef_switching`");
$routing_count['vrf']  = dbFetchCell("SELECT COUNT(vrf_id) from `vrfs`");

if ($_SESSION['userlevel'] >= '5' && ($routing_count['bgp']+$routing_count['ospf']+$routing_count['cef']+$routing_count['vrf']) > "0")
{
?>
     <li class="divider-vertical" style="margin:0;"></li>
     <li class="dropdown">
       <a href="<?php echo(generate_url(array('page'=>'routing'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="fugue-arrow-branch-000-left"></i> Routing <b class="caret"></b></a>
       <ul class="dropdown-menu">

<?php
  $separator = 0;

  if ($_SESSION['userlevel'] >= '5' && $routing_count['vrf'])
  {
    echo('<li><a href="routing/protocol=vrf/"><i class="sweetie-arrow-branch-byr"></i> VRFs</a></li>');
    $separator++;
  }

  if ($_SESSION['userlevel'] >= '5' && $routing_count['ospf'])
  {
    if ($separator)
    {
      echo('<li class="divider"></li>');
      $separator = 0;
    }
    echo('
        <li><a href="routing/protocol=ospf/"><img src="images/16/text_letter_omega.png" border="0" align="absmiddle" /> OSPF Devices </a></li>');
    $separator++;
  }
  // BGP Sessions
  if ($_SESSION['userlevel'] >= '5' && $routing_count['bgp'])
  {
    if ($separator)
    {
      echo('<li class="divider"></li>');
      $separator = 0;
    }
    echo('
        <li><a href="routing/protocol=bgp/type=all/graph=NULL/"><img src="images/16/link.png" border="0" align="absmiddle" /> BGP All Sessions </a></li>

        <li><a href="routing/protocol=bgp/type=external/graph=NULL/"><img src="images/16/world_link.png" border="0" align="absmiddle" /> BGP External</a></li>
        <li><a href="routing/protocol=bgp/type=internal/graph=NULL/"><img src="images/16/brick_link.png" border="0" align="absmiddle" /> BGP Internal</a></li>');
  }

  // Do Alerts at the bottom
  if ($bgp_alerts)
  {
    echo('
        <li class="divider"></li>
        <li><a href="routing/protocol=bgp/adminstatus=start/state=down/"><img src="images/16/link_error.png" border="0" align="absmiddle" /> Alerted BGP (' . $bgp_alerts . ')</a></li>
   ');
  }

  echo('      </ul></li>');

}

echo('            <li class="divider-vertical" style="margin:0;"></li>');


if ($config['api']['enabled'])
{
?>

    <li><a href="<?php echo(generate_url(array('page'=>'simpleapi'))); ?>" class="drop"><img src="images/16/page_white_code.png" border="0" align="absmiddle" /> Simple API</a>
      <div class="dropdown_1column">
        <div class="col_1">
          <ul>
            <li><a href="<?php echo(generate_url(array('page'=>'simpleapi'))); ?>"><img src="images/16/page_white_code.png" border="0" align="absmiddle" /> API Manual</a></li>
            <li><a href="<?php echo(generate_url(array('page'=>'simpleapi','api'=>'errorcodes'))); ?>"><img src="images/16/page_white_error.png" border="0" align="absmiddle" /> Error Codes</a></li>
          </ul>
        </div>
      </div>
    </li>


<?php
} # if ($api)
?>


          <li class="dropdown">
            <form id="searchform" class="navbar-search" action="" style="margin-left: 10px; margin-top: 5px; margin-bottom: -5px;">
              <input onkeyup="lookup(this.value);" type="text" value="" class="span2 dropdown-toggle" placeholder="Search" />
            </form>
            <div id="suggestions" class="dropdown-menu"></div>
          </li>

          </ul>

          <ul class="nav pull-right">

<?php

/**
$toggle_url_biggraphs = preg_replace('/(\?|\&)big_graphs=(yes|no)/', '', $_SERVER['REQUEST_URI']);
if (strstr($toggle_url_biggraphs,'?')) { $toggle_url_biggraphs .= '&amp;'; } else { $toggle_url_biggraphs .= '?'; }

if($_SESSION['big_graphs'] === 1)
{
  echo('<li><a href="' . $toggle_url_biggraps . 'big_graphs=no" title="Switch to normal graphs"><i class="menu-icon icon-th" style="font-size: 16px; color: #555;"></i></a></li>');
} else {
  echo('<li><a href="' . $toggle_url_biggraphs . 'big_graphs=yes" title="Switch to larger graphs"><i class="menu-icon icon-th-large" style="font-size: 16px; color: #555;"></i></a></li>');
}

$toggle_url_wide = preg_replace('/(\?|\&)widescreen=(yes|no)/', '', $_SERVER['REQUEST_URI']);
if (strstr($toggle_url_wide,'?')) { $toggle_url_wide .= '&amp;'; } else { $toggle_url_wide .= '?'; }

if($_SESSION['widescreen'] === 1)
{
  echo('<li><a href="' . $toggle_url_wide . 'widescreen=no" title="Switch to normal screen width layout"><i class="menu-icon icon-th" style="font-size: 16px; color: #555;"></i></a></li>');
} else {
  echo('<li><a href="' . $toggle_url_wide . 'widescreen=yes" title="Switch to wide screen layout"><i class="menu-icon icon-th-large" style="font-size: 16px; color: #555;"></i></a></li>');
}

**/

?>
            <li class="divider-vertical" style="margin:0;"></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><i class="fugue-gear"></i> <b class="caret"></b></a>
              <ul class="dropdown-menu">
<?php
$toggle_url_wide = preg_replace('/(\?|\&)widescreen=(yes|no)/', '', $_SERVER['REQUEST_URI']);
if (strstr($toggle_url_wide,'?')) { $toggle_url_wide .= '&amp;'; } else { $toggle_url_wide .= '?'; }

if($_SESSION['widescreen'] === 1)
{
  echo('<li><a href="' . $toggle_url_wide . 'widescreen=no" title="Switch to normal screen width layout"><i class="fugue-arrow-in" style="font-size: 16px; color: #555;"></i> Normal Width</a></li>');
} else {
  echo('<li><a href="' . $toggle_url_wide . 'widescreen=yes" title="Switch to wide screen layout"><i class="fugue-arrow-move" style="font-size: 16px; color: #555;"></i> Widescreen</a></li>');
}

$toggle_url_biggraphs = preg_replace('/(\?|\&)big_graphs=(yes|no)/', '', $_SERVER['REQUEST_URI']);
if (strstr($toggle_url_biggraphs,'?')) { $toggle_url_biggraphs .= '&amp;'; } else { $toggle_url_biggraphs .= '?'; }

if($_SESSION['big_graphs'] === 1)
{
  echo('<li><a href="' . $toggle_url_biggraps . 'big_graphs=no" title="Switch to normal graphs"><i class="fugue-layout-6" style="font-size: 16px; color: #555;"></i> Normal Graphs</a></li>');
} else {
  echo('<li><a href="' . $toggle_url_biggraphs . 'big_graphs=yes" title="Switch to larger graphs"><i class="fugue-layout-4" style="font-size: 16px; color: #555;"></i> Large Graphs</a></li>');
}


?>

              </ul>
            </li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div>
    </div><!-- /navbar-inner -->
  </div>


<script>

function lookup(inputString) {
   if(inputString.length == 0) {
      $('#suggestions').fadeOut(); // Hide the suggestions box
   } else {
      $.post("ajax_search.php", {queryString: ""+inputString+""}, function(data) { // Do an AJAX call
         $('#suggestions').fadeIn(); // Show the suggestions box
         $('#suggestions').html(data); // Fill the suggestions box
      });
   }
}

</script>
