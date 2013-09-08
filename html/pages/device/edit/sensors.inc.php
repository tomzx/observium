<?php

$sensors = dbFetchRows("SELECT * FROM `sensors`,`sensors-state` WHERE `device_id` = ? AND `sensors-state`.`sensor_id` = `sensors`.`sensor_id` 
                        ORDER BY `sensor_type`,`sensor_class`,`sensor_index` ", array($device['device_id']));

if ($_POST['submit'] == "update-sensors" && $_SESSION['userlevel'] == '10')
{
  foreach ($sensors AS $sensor)
  {
    if ($_POST['sensors'][$sensor['sensor_id']]['sensor_ignore'] == "on") { $_POST['sensors'][$sensor['sensor_id']]['sensor_ignore'] = "1"; } else { $_POST['sensors'][$sensor['sensor_id']]['sensor_ignore'] = "0"; }

    foreach (array('sensor_ignore','sensor_limit_low','sensor_limit') as $field)
    {
      if ($_POST['sensors'][$sensor['sensor_id']][$field]    != $sensor[$field])    { $sup[$field] = $_POST['sensors'][$sensor['sensor_id']][$field]; }
    }

    if (is_array($sup))
    {
      dbUpdate($sup, 'sensors', '`sensor_id` = ?', array($sensor['sensor_id']));
      $did_update = TRUE;
    }
    unset($sup);
  }

  $sensors = dbFetchRows("SELECT * FROM `sensors`,`sensors-state` WHERE `device_id` = ? AND `sensors-state`.`sensor_id` = `sensors`.`sensor_id` 
                          ORDER BY `sensor_type`,`sensor_class`,`sensor_index` ", array($device['device_id']));
}

#print_vars($_POST);

?>

<form id='update-sensors' name='update-sensors' method='post' action=''>
<fieldset>
  <legend>Sensor Properties</legend>

<table class="table table-bordered table-striped table-condensed">
  <thead>
    <tr>
      <th width="60">Index</th>
      <th width="120">MIB Type</th>
      <th width="100">Class</th>
      <th>Descr</th>
      <th width="60">Current</th>
      <th width="60">Min</th>
      <th width="60">Max</th>
      <th width="50">Alerts</th>
    </tr>
  </thead>
  <tbody>

<?php
$row=1;
foreach ($sensors as $sensor)
{

  echo('<tr>');
  echo('<td>'.htmlentities($sensor['sensor_index']).'</td>');
  echo('<td>'.$sensor['sensor_type'].'</td>');
  echo('<td>'.$sensor['sensor_class'].'</td>');
  echo('<td>'.htmlentities($sensor['sensor_descr']).'</td>');
  echo('<td>'.htmlentities($sensor['sensor_value']).$config['sensor_types'][$sensor['sensor_class']]['symbol'].'</td>');
  echo('<td><input class="input-mini" name="sensors['.$sensor['sensor_id'].'][sensor_limit_low]" size="4" value="'.htmlentities($sensor['sensor_limit_low']).'"></input></td>');
  echo('<td><input class="input-mini" name="sensors['.$sensor['sensor_id'].'][sensor_limit]" size="4" value="'.htmlentities($sensor['sensor_limit']).'"></input></td>');
#  echo('<td><input type=checkbox name="sensors['.$sensor['sensor_id'].'][sensor_ignore]"' . ($sensor['sensor_ignore'] ? 'checked' : '') . '></td>');
  echo('<td><div id="sensors['.$sensor['sensor_id'].'][sensor_ignore]" class="switch switch-mini" data-on="danger" data-off="success" data-on-label="No" data-off-label="Yes">
             <input type=checkbox name="sensors['.$sensor['sensor_id'].'][sensor_ignore]"'.($sensor['sensor_ignore'] ? "checked" : "").'></div></td>');
  echo('</tr>');
}
?>

</tbody>
</table>
</fieldset>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="update-sensors"><i class="icon-ok icon-white"></i> Save Changes</button>
  </div>
</form>
