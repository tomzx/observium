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
   include("pages/device/apps/".mres($vars['app']).".inc.php");
}

$pagetitle[] = "Apps";

?>
