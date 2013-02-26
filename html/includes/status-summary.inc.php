<?php

// FIXME queries such as the one below should probably go into index.php?

if($_SESSION['userlevel'] >= 5)
{
  $devices['up']        = dbFetchCell("SELECT COUNT(*) FROM devices  WHERE status = '1' AND `ignore` = '0'  AND `disabled` = '0'");
  $devices['down']      = dbFetchCell("SELECT COUNT(*) FROM devices WHERE status = '0' AND `ignore` = '0'  AND `disabled` = '0'");
  $devices['ignored']   = dbFetchCell("SELECT COUNT(*) FROM devices WHERE `ignore` = '1'  AND `disabled` = '0'");
  $devices['disabled']  = dbFetchCell("SELECT COUNT(*) FROM devices WHERE `disabled` = '1'");

  $ports['count']       = dbFetchCell("SELECT COUNT(*) FROM ports WHERE deleted = '0'");
  $ports['up']          = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND I.ifOperStatus = 'up' AND I.ignore = '0' AND I.device_id = D.device_id AND D.ignore = '0'");
  $ports['down']        = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND I.ifOperStatus = 'down' AND I.ifAdminStatus = 'up' AND I.ignore = '0' AND D.device_id = I.device_id AND D.ignore = '0'");
  $ports['shutdown']    = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND I.ifAdminStatus = 'down' AND I.ignore = '0' AND D.device_id = I.device_id AND D.ignore = '0'");
  $ports['ignored']     = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND D.device_id = I.device_id AND (I.ignore = '1' OR D.ignore = '1')");
  $ports['errored']     = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND D.device_id = I.device_id AND (I.ignore = '0' OR D.ignore = '0') AND (I.ifInErrors_delta > '0' OR I.ifOutErrors_delta > '0')");

  $services['count']    = dbFetchCell("SELECT COUNT(service_id) FROM services");
  $services['up']       = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_status = '1' AND service_ignore ='0'");
  $services['down']     = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_status = '0' AND service_ignore = '0'");
  $services['ignored']  = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_ignore = '1'");
  $services['disabled'] = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_disabled = '1'");

  $sensors['count']    = dbFetchCell("SELECT COUNT(sensor_id) FROM sensors");
  $sensors['up']       = dbFetchCell("SELECT COUNT(sensor_id) FROM sensors WHERE sensor_status = '1' AND sensor_ignore ='0'");
  $sensors['down']     = dbFetchCell("SELECT COUNT(sensor_id) FROM sensors WHERE sensor_status = '0' AND sensor_ignore = '0'");
  $sensors['ignored']  = dbFetchCell("SELECT COUNT(sensor_id) FROM sensors WHERE sensor_ignore = '1'");
  $sensors['disabled'] = dbFetchCell("SELECT COUNT(sensor_id) FROM sensors WHERE sensor_disabled = '1'");



}
else
{
  $devices['count']       = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id", array($_SESSION['user_id']));
  $devices['up']          = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.status = '1' AND D.ignore = '0'", array($_SESSION['user_id']));
  $devices['down']        = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.status = '0' AND D.ignore = '0'", array($_SESSION['user_id']));
  $devices['ignored']     = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.ignore = '1' AND D.disabled = '0'", array($_SESSION['user_id']));
  $devices['disabled']    = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.ignore = '1'", array($_SESSION['user_id']));

  $ports['count']    = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id", array($_SESSION['user_id']));
  $ports['up']       = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND ifOperStatus = 'up'", array($_SESSION['user_id']));
  $ports['down']     = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up'", array($_SESSION['user_id']));
  $ports['shutdown'] = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND I.ifAdminStatus = 'down' AND I.ignore = '0'", array($_SESSION['user_id']));
  $ports['ignored']  = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND D.device_id = I.device_id AND (I.ignore = '1' OR D.ignore = '1')", array($_SESSION['user_id']));
  $ports['disabled'] = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND ifAdminStatus = 'down'", array($_SESSION['user_id']));
  $ports['errored']  = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND (I.in_errors > '0' OR I.out_errors > '0')", array($_SESSION['user_id']));

  $services['count']      = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id", array($_SESSION['user_id']));
  $services['up']         = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_status = '1' AND service_ignore ='0'", array($_SESSION['user_id']));
  $services['down']       = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_status = '0' AND service_ignore = '0'", array($_SESSION['user_id']));
  $services['ignored']       = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_ignore = '1'", array($_SESSION['user_id']));
  $services['disabled']   = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_disabled = '1'", array($_SESSION['user_id']));
}

if ($devices['down'])  { $devices['class'] = "error"; } else { $devices['class'] = ""; }
if ($ports['down'])    { $ports['class'] = "error"; } else { $ports['class'] = ""; }
if ($services['down']) { $services['class'] = "error"; } else { $services['class'] = ""; }

?>

<div class="row-fluid">
<div class="span8">
<table class="table table-bordered table-condensed-more table-rounded table-striped">
  <thead>
    <tr>
     <th></th>
     <th>Total</th>
     <th>Up</th>
     <th>Down</th>
     <th>Ignored</th>
     <th>Disabled</th>
    </tr>
  </tead>
  <tbody>
   <tr class="<?php echo($devices['class']); ?>">
     <td><strong><a href="<?php echo(generate_url(array('page' => 'devices'))); ?>">Devices</a></strong></td>
     <td><a href="<?php echo(generate_url(array('page' => 'devices'))); ?>"><?php echo($devices['count']) ?></a></td>
     <td><a class="green" href="<?php echo(generate_url(array('page' => 'devices', 'status' => '1'))); ?>"><?php echo($devices['up']) ?> up</a></td>
     <td><a class="red" href="<?php echo(generate_url(array('page' => 'devices', 'status' => '0'))); ?>"><?php echo($devices['down']) ?> down</a></td>
     <td><a class="black" href="<?php echo(generate_url(array('page' => 'devices', 'ignore' => '1'))); ?>"><?php echo($devices['ignored']) ?> ignored</a> </td>
     <td><a class="grey" href="<?php echo(generate_url(array('page' => 'devices', 'disabled' => '1'))); ?>"><?php echo($devices['disabled']) ?> disabled</a></td>
    </tr>
    <tr class="<?php echo($ports['class']) ?>">
      <td><strong><a href="<?php echo(generate_url(array('page' => 'ports'))); ?>">Ports</a></strong></td>
      <td><a href="<?php echo(generate_url(array('page' => 'ports'))); ?>"><?php echo($ports['count']) ?></td>
      <td><a class="green" href="<?php echo(generate_url(array('page' => 'ports', 'state' => 'up'))); ?>"><?php echo($ports['up']) ?> up </a></td>
      <td><a class="red" href="<?php echo(generate_url(array('page' => 'ports', 'state' => 'down'))); ?>"><?php echo($ports['down']) ?> down </a></td>
      <td><a class="black" href="<?php echo(generate_url(array('page' => 'ports', 'ignore' => '1'))); ?>"><?php echo($ports['ignored']) ?> ignored </a></td>
      <td><a class="grey" href="<?php echo(generate_url(array('page' => 'ports', 'state' => 'admindown'))); ?>"><?php echo($ports['shutdown']) ?> shutdown</a></td>
  </tr>
  </tbody>
</table>
</div>

<?php

/**

<div class="span4">
  <table class="table table-bordered table-condensed-more table-rounded table-striped">
  <thead>
    <tr>
     <th></th>
     <th>Total</th>
     <th>Ok</th>
     <th>Fail</th>
    </tr>
  </tead>
  <tbody>
    <tr class="<?php echo($bgp['class']); ?>">
        <td><strong><a href="<?php echo(generate_url(array('page' => 'routing', 'protocol' => 'bgp'))); ?>">BGP Sessions</a></strong></td>
        <td><a href="<?php echo(generate_url(array('page' => 'devices'))); ?>"><?php echo($devices['count']) ?></a></td>
        <td><a href="<?php echo(generate_url(array('page' => 'devices'))); ?>"><?php echo($devices['count']) ?></a></td>
        <td><a href="<?php echo(generate_url(array('page' => 'devices'))); ?>"><?php echo($devices['count']) ?></a></td>
      </tr>
    <tr class="<?php echo($bgp['class']); ?>">
        <td><strong><a href="<?php echo(generate_url(array('page' => 'sensors'))); ?>">Sensors</a></strong></td>
        <td><a href="<?php echo(generate_url(array('page' => 'sensors'))); ?>"><?php echo($sensors['count']) ?></a></td>
        <td><a href="<?php echo(generate_url(array('page' => 'sensors'))); ?>"><?php echo($sensors['count']) ?></a></td>
        <td><a href="<?php echo(generate_url(array('page' => 'sensors'))); ?>"><?php echo($sensors['count']) ?></a></td>
      </tr>
    </tbody>
  </table>
</div>
</div>

**/

?>
