<?php

/// If we've been given a 'port' variable, try to work out the id from this, as it's the ifAlias
if (is_array($device) && empty($vars['id']) && !empty($vars['port']))
{
  $vars['id'] = get_port_id_by_ifDescr($device['device_id'], $vars['port']);
}

if (is_numeric($vars['id']) && ($auth || port_permitted($vars['id'])))
{
  $port   = get_port_by_id($vars['id']);
  $device = device_by_id_cache($port['device_id']);
  $title  = generate_device_link($device);
  $title .= " :: Port  <b>".generate_port_link($port) ."</b>";

  $graph_title = shorthost($device['hostname']) . " ::" . strtolower(makeshortif($port['ifDescr']))."";
  $auth   = TRUE;
  $rrd_filename = get_port_rrdfilename($device, $port);
}

?>
