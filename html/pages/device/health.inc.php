<?php

$datas[] = 'overview';

if (dbFetchCell("select count(*) from processors WHERE device_id = ?", array($device['device_id']))) { $datas[] = 'processor'; }
if (dbFetchCell("select count(*) from mempools WHERE device_id = ?", array($device['device_id']))) { $datas[] = 'mempool'; }
if (dbFetchCell("select count(*) from storage WHERE device_id = ?", array($device['device_id']))) { $datas[] = 'storage'; }
if (dbFetchCell("select count(*) from ucd_diskio WHERE device_id = ?", array($device['device_id']))) { $datas[] = 'diskio'; }

$sensors_device = dbFetchRows("SELECT sensor_class FROM sensors WHERE device_id=? GROUP BY sensor_class", array($device['device_id']));
foreach ($sensors_device as $sensor)
{
  $datas[] = $sensor['sensor_class'];
}

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab'     => 'health');

if (!$vars['metric']) { $vars['metric'] = "overview"; }

$navbar['brand'] = "Health";
$navbar['class'] = "navbar-narrow";

foreach ($datas as $type)
{
  if ($vars['metric'] == $type) { $navbar['options'][$type]['class'] = "active"; }
  $navbar['options'][$type]['url']  = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'health', 'metric' => $type));
  $navbar['options'][$type]['text'] = nicecase($type);
}

print_navbar($navbar);

if (is_file("pages/device/health/".mres($vars['metric']).".inc.php"))
{
   include("pages/device/health/".mres($vars['metric']).".inc.php");
} else {

  echo('<table class="table table-condensed table-striped table-hover table-bordered">');

  foreach ($datas as $type)
  {
    if ($type != "overview")
    {

      $graph_title = $type_text[$type];
      $graph_array['type'] = "device_".$type;

      include("includes/print-device-graph.php");
    }
  }
  echo('</table>');
}

$pagetitle[] = "Health";

?>
