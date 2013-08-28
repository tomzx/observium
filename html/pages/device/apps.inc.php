<?php

$link_array = array('page' => 'device', 'device'  => $device['device_id'], 'tab' => 'apps');

$navbar = array();
$navbar['brand'] = "Apps";
$navbar['class'] = "navbar-narrow";

$i=1;
foreach (dbFetchRows("SELECT * FROM `applications` WHERE `device_id` = ?", array($device['device_id'])) as $app)
{
  $i++;
  if (!$vars['app']) { $vars['app'] = $app['app_type']; }

  if (!empty($app['app_instance']))
  {
    $text .= "(".$app['app_instance'].")";
    $app['link_array']['instance'] = $app['app_id'];
  }

  $navbar['options'][$i]['url']  = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'apps', 'app' => $app['app_type'], 'instance' => $app['app_id'] ));
  $navbar['options'][$i]['text'] = nicecase($app['app_type']);
  if ($vars['app'] == $app['app_type']) { $navbar['options'][$i]['class'] = "active"; }

}
print_navbar($navbar);
unset($navbar);

$where_array = array($device['device_id'], $vars['app']);
if($vars['instance'])
{
  $where = " AND `app_id` = ?";
  $where_array[] = $vars['instance'];
}

$app = dbFetchRow("SELECT * FROM `applications` WHERE `device_id` = ? AND `app_type` = ?".$where, $where_array);

if (is_file("pages/device/apps/".mres($vars['app']).".inc.php"))
{

  // Include app code to output data
  include("pages/device/apps/".mres($vars['app']).".inc.php");

  // If an $app_sections array has been returned, build a menu
  if(isset($app_sections) && is_array($app_sections))
  {
    $navbar['brand'] = nicecase(mres($vars['app']));
    $navbar['class'] = "navbar-narrow";

    foreach ($app_sections as $app_section => $text)
    {
      // Set the chosen app to be this one if it's not already set.
      if (!$vars['app_section']) { $vars['app_section'] = $app_section; }
      if ($vars['app_section'] == $app_section) { $navbar['options'][$app_section]['class'] = "active"; }

      $navbar['options'][$app_section]['url']  = generate_url($vars, array('app_section' => $app_section));
      $navbar['options'][$app_section]['text'] = nicecase($app_section);
    }
    print_navbar($navbar);
    unset($navbar);
  } else {
    // It appears this app doesn't have multiple sections. We set app_section to default here.
    $vars['app_section'] = 'default';
  }

  // If a matching app_section array exists within app_graphs, print the graphs.
  if(isset($app_graphs[$vars['app_section']]) && is_array($app_graphs[$vars['app_section']]))
  {

    echo '<table class="table table-striped table-hover">';

    foreach ($app_graphs[$vars['app_section']] as $key => $text) {
      $graph_type            = $key;
      $graph_array['to']     = $config['time']['now'];
      $graph_array['id']     = $app['app_id'];
      $graph_array['type']   = "application_".$key;
      echo '<tr><td>';
      echo '<h4>',$text,'</h4>';

      print_graph_row($graph_array);

      echo '</td></tr>';
    }

    echo '</table>';
  }

}

$pagetitle[] = "Apps";
