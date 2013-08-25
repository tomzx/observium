<?php

$overview = 1;

$ports['total']    = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE device_id = ?", array($device['device_id']));
$ports['up']       = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE device_id = ? AND `ifOperStatus` = 'up'", array($device['device_id']));
$ports['down']     = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE device_id = ? AND `ifOperStatus` = 'down' AND `ifAdminStatus` = 'up'", array($device['device_id']));
$ports['disabled'] = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE device_id = ? AND `ifAdminStatus` = 'down'", array($device['device_id']));

$services['total']    = dbFetchCell("SELECT COUNT(service_id) FROM `services` WHERE `device_id` = ?", array($device['device_id']));
$services['up']       = dbFetchCell("SELECT COUNT(service_id) FROM `services` WHERE `device_id` = ? AND `service_status` = '1' AND `service_ignore` ='0'", array($device['device_id']));
$services['down']     = dbFetchCell("SELECT COUNT(service_id) FROM `services` WHERE `device_id` = ? AND `service_status` = '0' AND `service_ignore` = '0'", array($device['device_id']));
$services['disabled'] = dbFetchCell("SELECT COUNT(service_id) FROM `services` WHERE `device_id` = ? AND `service_ignore` = '1'", array($device['device_id']));

if ($services['down']) { $services_colour = $warn_colour_a; } else { $services_colour = $list_colour_a; }
if ($ports['down']) { $ports_colour = $warn_colour_a; } else { $ports_colour = $list_colour_a; }
?>

<div class="row">
<div class="col-md-6">

<?php include("overview/information.inc.php"); ?>

<?php

include("overview/ports.inc.php");

if ($services['total'])
{
?>

<div class="well info_box">
    <div class="title"><i class="oicon-network-ethernet"></i> Services</div>
    <div class="content">

<?php

  echo("
<table class='table table-condensed-more table-striped'>
<tr bgcolor=$services_colour align=center><td></td>
<td width=25%><img src='images/16/cog.png' align=absmiddle> $services[total]</td>
<td width=25% class=green><img src='images/16/cog_go.png' align=absmiddle> $services[up]</td>
<td width=25% class=red><img src='images/16/cog_error.png' align=absmiddle> $services[down]</td>
<td width=25% class=grey><img src='images/16/cog_disable.png' align=absmiddle> $services[disabled]</td></tr>
</table>");

  echo("<div style='padding: 8px; font-size: 11px; font-weight: bold;'>");

  foreach (dbFetchRows("SELECT * FROM services WHERE device_id = ? ORDER BY service_type", array($device['device_id'])) as $data)
  {
    if ($data['service_status'] == "0" && $data['service_ignore'] == "1") { $status = "grey"; }
    if ($data['service_status'] == "1" && $data['service_ignore'] == "1") { $status = "green"; }
    if ($data['service_status'] == "0" && $data['service_ignore'] == "0") { $status = "red"; }
    if ($data['service_status'] == "1" && $data['service_ignore'] == "0") { $status = "blue"; }
    echo("$break<a class=$status>" . strtolower($data['service_type']) . "</a>");
    $break = ", ";
  }

  echo("</div>");
  echo("</div></div>");
}

if ($config['enable_syslog'])
{
  $syslog =  dbFetchCell("SELECT COUNT(*) from syslog WHERE device_id = ?", array($device['device_id']));
  if ($syslog)
  { ?>

    <div class="well info_box">
      <div class="title"><a href="<?php echo(generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'logs', 'section' => 'syslog'))); ?>">
        <i class="oicon-clipboard-eye"></i> Syslog</a></div>
      <div class="content">

    <?php
    print_syslogs(array('device' => $device['device_id'], 'short' => TRUE));
    ?>
      </div>
    </div>
<?php
  }
}

echo("</div>");

echo('<div class="col-md-6">');

// Right Pane
include("overview/processors.inc.php");
if (is_array($device_state['ucd_mem']))
{
  include("overview/ucd_mem.inc.php");
} else {
  include("overview/mempools.inc.php");
}

include("overview/storage.inc.php");

if (is_array($entity_state['group']['c6kxbar'])) { include("overview/c6kxbar.inc.php"); }

include("overview/toner.inc.php");
include("overview/sensors/temperatures.inc.php");
include("overview/sensors/humidity.inc.php");
include("overview/sensors/fanspeeds.inc.php");
include("overview/sensors/dbm.inc.php");
include("overview/sensors/voltages.inc.php");
include("overview/sensors/current.inc.php");
include("overview/sensors/power.inc.php");
include("overview/sensors/frequencies.inc.php");

// Start events
?>
   <div class="well info_box">
      <div class="title"><a href="<?php echo(generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'logs', 'section' => 'eventlog'))); ?>">
        <i class="oicon-clipboard-audit"></i> Events</a></div>
      <div class="content">
<?php
      print_events(array('device' => $device['device_id'], 'pagesize' => 15, 'short' => TRUE));
?>
    </div>
  <div>
</div>
</div>
