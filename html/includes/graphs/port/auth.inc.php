<?php

// Get port ID by ifIndex/ifDescr/ifAlias or customer circuit
// variables: ifindex, ifdescr (or port), ifalias, circuit
if (!is_numeric($vars['id']))
{
  if (is_numeric($device['device_id']))
  {
    if (is_numeric($vars['ifindex']))
    {
      // Get port by ifIndex
      $port = get_port_by_index_cache($device['device_id'], $vars['ifindex']);
      if ($port) { $vars['id'] = $port['port_id']; }
    } elseif (!empty($vars['ifdescr']))
    {
      // Get port by ifDescr
      $port_id = get_port_id_by_ifDescr($device['device_id'], $vars['ifdescr']);
      if ($port_id) { $vars['id'] = $port_id; }
    } elseif (!empty($vars['port']))
    {
      // Get port by ifDescr (backward compatibility)
      $port_id = get_port_id_by_ifDescr($device['device_id'], $vars['port']);
      if ($port_id) { $vars['id'] = $port_id; }
    } elseif (!empty($vars['ifalias']))
    {
      // Get port by ifAlias
      $port_id = get_port_id_by_ifAlias($device['device_id'], $vars['ifalias']);
      if ($port_id) { $vars['id'] = $port_id; }
    }
  } elseif (!empty($vars['circuit']))
  {
    // Get port by circuit id
    $port_id = get_port_id_by_customer(array('circuit' => $vars['circuit']));
    if ($port_id) { $vars['id'] = $port_id; }
  }
}

if (is_numeric($vars['id']) && ($auth || port_permitted($vars['id'])))
{
  $port   = get_port_by_id($vars['id']);
  $device = device_by_id_cache($port['device_id']);
  $title  = generate_device_link($device);
  $title .= " :: Port  <b>".generate_port_link($port) ."</b>";

  $graph_title = shorthost($device['hostname']) . " :: " . strtolower(makeshortif($port['ifDescr']))."";
  $auth   = TRUE;
  $rrd_filename = get_port_rrdfilename($device, $port);
}

?>
