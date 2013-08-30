<?php

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'edit');

if ($_SESSION['userlevel'] < '7')
{
  print_error("Insufficient Privileges");
} else {

  $panes['device']   = 'Device Settings';
  $panes['snmp']     = 'SNMP';
  $panes['ssh']     = 'SSH';
  $panes['ports']    = 'Ports';
  $panes['sensors']   = "Sensors";

  if (count($config['os'][$device['os']]['icons']))
  {
    $panes['icon']  = 'Icon';
  }

  $panes['apps']     = 'Applications';
  $panes['alerts']   = 'Alerts';
  $panes['modules']  = 'Modules';

  if ($config['enable_services'])
  {
    $panes['services'] = 'Services';
  }

  if ($device_loadbalancer_count['netscaler_vsvr']) { $panes['netscaler_vsvrs'] = 'NS vServers'; }
  if ($device_loadbalancer_count['netscaler_services']) { $panes['netscaler_svcs'] = 'NS Services'; }

  $panes['ipmi']     = 'IPMI';

  $navbar['brand'] = "Edit";
  $navbar['class'] = "navbar-narrow";

  foreach ($panes as $type => $text)
  {
    if (!isset($vars['section'])) { $vars['section'] = $type; }

    if ($vars['section'] == $type) { $navbar['options'][$type]['class'] = "active"; }
    $navbar['options'][$type]['url']  = generate_url($link_array,array('section'=>$type));
    $navbar['options'][$type]['text'] = $text;
  }
  $navbar['options_right']['delete']['url']  = generate_url($link_array,array('section'=>'delete'));
  $navbar['options_right']['delete']['text'] = 'Delete';
  if ($vars['section'] == 'delete') { $navbar['options_right']['delete']['class'] = 'active'; }
  print_navbar($navbar);

  if (is_file("pages/device/edit/".mres($vars['section']).".inc.php"))
  {
    include("pages/device/edit/".mres($vars['section']).".inc.php");
  }
}

$pagetitle[] = "Settings";

?>
