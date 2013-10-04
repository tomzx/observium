<?php

$datas = array('processor','mempool','storage');

if ($toner_exists) { $datas[] = 'toner'; }

foreach (array_keys($config['sensor_types']) as $type)
{
  if ($used_sensors[$type]) { $datas[] = $type; }
}

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
  echo(generate_link(nicecase($metric),$link_array,array('metric'=> $metric, 'view' => $vars['view'])));
  echo('</li>');

}

echo('</ul><ul class="nav pull-right">');

if ($vars['view'] == "graphs")
{
  $class = "active";
} else {
  unset($class);
}

echo('<li class="pull-right '.$class.'">');
echo(generate_link("Graphs",$link_array,array('metric'=> $vars['metric'], 'view' => "graphs")));
echo('</li>');

if ($vars['view'] != "graphs")
{
  $class = "active";
} else {
  unset($class);
}

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
  $sensor_type = $vars['metric'];

  if (file_exists('pages/health/'.$vars['metric'].'.inc.php'))
  {
    include('pages/health/'.$vars['metric'].'.inc.php');
  } else {
    include('pages/health/sensors.inc.php');
  }
}
else
{
  echo("No sensors of type " . $vars['metric'] . " found.");
}

// EOF
