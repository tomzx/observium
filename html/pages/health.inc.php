<?php

$datas = array('processor','mempool','storage');

if ($toner_exists) $datas[] = 'toner';

if ($used_sensors['temperature']) $datas[] = 'temperature';
if ($used_sensors['humidity']) $datas[] = 'humidity';
if ($used_sensors['fanspeed']) $datas[] = 'fanspeed';
if ($used_sensors['voltage']) $datas[] = 'voltage';
if ($used_sensors['frequency']) $datas[] = 'frequency';
if ($used_sensors['current']) $datas[] = 'current';
if ($used_sensors['power']) $datas[] = 'power';
if ($used_sensors['dbm']) $datas[] = 'dbm';

// FIXME generalize -> static-config ?
$type_text['overview'] = "Overview";
$type_text['temperature'] = "Temperature";
$type_text['humidity'] = "Humidity";
$type_text['mempool'] = "Memory";
$type_text['storage'] = "Disk Usage";
$type_text['diskio'] = "Disk I/O";
$type_text['processor'] = "Processor";
$type_text['voltage'] = "Voltage";
$type_text['fanspeed'] = "Fanspeed";
$type_text['frequency'] = "Frequency";
$type_text['current'] = "Current";
$type_text['power'] = "Power";
$type_text['toner'] = "Toner";
$type_text['dbm'] = "dBm";

if (!$vars['metric']) { $vars['metric'] = "processor"; }
if (!$vars['view']) { $vars['view'] = "detail"; }

$link_array = array('page'    => 'health');

$pagetitle[] = "Health";
?>

<div class="navbar navbar-narrow">
  <div class="navbar-inner">
    <a class="brand">Health</a>
    <ul class="nav">

<?php
$sep = "";
foreach ($datas as $texttype)
{
  $metric = strtolower($texttype);
  if ($vars['metric'] == $metric)
  {
    $class = "active";
  } else { unset($class); }

  echo('<li class="'.$class.'">');
  echo(generate_link($type_text[$metric],$link_array,array('metric'=> $metric, 'view' => $vars['view'])));
  echo('</li>');

}
echo('</ul><ul class="nav pull-right">');
if ($vars['view'] == "graphs")
{
    $class = "active";
  } else { unset($class); }

  echo('<li class="pull-right '.$class.'">');
  echo(generate_link("Graphs",$link_array,array('metric'=> $vars['metric'], 'view' => "graphs")));
  echo('</li>');

if ($vars['view'] != "graphs")
{
    $class = "active";
  } else { unset($class); }

  echo('<li class="pull-right '.$class.'">');
  echo(generate_link("No Graphs",$link_array,array('metric'=> $vars['metric'], 'view' => "detail")));
  echo('</li>');

echo('</ul></div></div>');

if (in_array($vars['metric'],array_keys($used_sensors))
  || $vars['metric'] == 'processor'
  || $vars['metric'] == 'storage'
  || $vars['metric'] == 'toner'
  || $vars['metric'] == 'mempool')
{
  include('pages/health/'.$vars['metric'].'.inc.php');
}
else
{
  echo("No sensors of type " . $vars['metric'] . " found.");
}

?>
