<?php

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'apps');

?>

<div class="navbar navbar-narrow">
  <div class="navbar-inner">
    <a class="brand">Apps</a>
    <ul class="nav">
    <li class="divider-vertical"></li>

<?php
/// FIXME - standardise and function?
foreach ($app_list as $app)
{
  if ($vars['app'] == $app['app_type'])
  {
    $class = "active";
  } else { unset($class); }
  echo('<li class="'.$class.'">'.generate_link(nicecase($app['app_type']),array('page'=>'apps','app'=>$app['app_type'])).'</li>');
}
?>

    </ul>
  </div>
</div>

<?php
if($vars['app'])
{
  if (is_file("pages/apps/".mres($vars['app']).".inc.php"))
  {
    include("pages/apps/".mres($vars['app']).".inc.php");
  } else {
    include("pages/apps/default.inc.php");
  }
} else {
  include("pages/apps/overview.inc.php");
}

$pagetitle[] = "Apps";
?>
