<?php

/// FIXME this list should come from somewhere else; these are also kind of duplicated more eloquently in device/apps
$graphs['apache']            = array('bits', 'hits', 'scoreboard', 'cpu');
$graphs['drbd']              = array('disk_bits', 'network_bits', 'queue', 'unsynced');
$graphs['mysql']             = array('network_traffic', 'connections', 'command_counters', 'select_types');
$graphs['memcached']         = array('bits', 'commands', 'data', 'items');
$graphs['powerdns']          = array('recursing', 'queries', 'querycache', 'latency');
/// FIXME ^ recursing should be replaced by something else probably; we should have the recursor as app and as such get such stats there.
$graphs['ntpd']              = array('stats', 'freq', 'stratum', 'bits');
$graphs['postgresql']        = array('xact', 'blks', 'tuples', 'tuples_query');
$graphs['shoutcast']         = array('multi_stats', 'multi_bits');
$graphs['nginx']             = array('connections', 'req');
$graphs['unbound']           = array('queries', 'queue', 'memory', 'qtype');
$graphs['freeradius']        = array('access');
$graphs['powerdns-recursor'] = array('queries', 'timeouts', 'cache', 'latency');
$graphs['exim-mailqueue']    = array('total');
$graphs['zimbra']            = array('threads','mtaqueue'); # FIXME 2 more!

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
