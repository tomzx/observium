<?php

$sensors = dbFetchRows("SELECT * FROM `sensors`,`sensors-state` WHERE `device_id` = ? AND `sensors-state`.`sensor_id` = `sensors`.`sensor_id` ORDER BY `sensor_type`,`sensor_class`,`sensor_index` ", array($device['device_id']));

if ($_POST['submit'] == "update-sensors")
{
  if ($_SESSION['userlevel'] == '10')
  {
    $su = array();
    foreach ($_POST as $key => $value) {
      list($field, $index) = explode("-", $key);
      #if($field == "sensor_ignore" && $value == "on") { $value = "1"; } elseif($field == "sensor_ignore") { $value = "0"; }
      $su[$index][$field] = $value;
    }
  }

  foreach ($sensors AS $sensor)
  {
    $sid = $sensor['sensor_id'];
    if($su[$sid]['sensor_ignore'] == "on") { $su[$sid]['sensor_ignore'] = "1"; } else { $su[$sid]['sensor_ignore'] = "0"; }

    foreach(array('sensor_ignore','sensor_limit_low','sensor_limit') as $field)
    {
      if($su[$sid][$field]    != $sensor[$field])    { $sup[$field] = $su[$sid][$field]; }
    }

    if(is_array($sup))
    {
      dbUpdate($sup, 'sensors', '`sensor_id` = ?', array($sensor['sensor_id']));
      $did_update = TRUE;
    }
    unset($sup);
  }
}

if ($did_update)
{
  $sensors = dbFetchRows("SELECT * FROM `sensors`,`sensors-state` WHERE `device_id` = ? AND `sensors-state`.`sensor_id` = `sensors`.`sensor_id` ORDER BY `sensor_type`,`sensor_class`,`sensor_index` ", array($device['device_id']));
}
?>

<form id='update-sensors' name='update-sensors' method='post' action=''>
<fieldset>
  <legend>Sensor Properties</legend>

<table class="table table-bordered table-striped table-condensed">
  <thead>
    <tr>
      <th width="60">Index</th>
      <th width="100">Type</th>
      <th width="100">Class</th>
      <th>Descr</th>
      <th width="60">Current</th>
      <th width="60">Min</th>
      <th width="60">Max</th>
      <th width="50">Ignore</th>
    </tr>
  </thead>
  <tbody>

<?php
$row=1;
foreach ($sensors as $sensor)
{

  echo('<tr>');
  echo('<td>'.$sensor['sensor_index'].'</td>');
  echo('<td>'.$sensor['sensor_type'].'</td>');
  echo('<td>'.$sensor['sensor_class'].'</td>');
  echo('<td>'.$sensor['sensor_descr'].'</td>');
  echo('<td>'.$sensor['sensor_value'].$config['sensor_classes'][$sensor['sensor_class']].'</td>');
  echo('<td><input class="input-mini" name="sensor_limit_low-'.$sensor['sensor_id'].'" size="4" value="'.$sensor['sensor_limit_low'].'"></input></td>');
  echo('<td><input class="input-mini" name="sensor_limit-'.$sensor['sensor_id'].'" size="4" value="'.$sensor['sensor_limit'].'"></input></td>');
  echo("<td><input type=checkbox name='sensor_ignore-".$sensor['sensor_id']."'".($sensor['sensor_ignore'] ? 'checked' : '')."></td>");
  echo('</tr>');
}
?>

</tbody>
</table>
</fieldset>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="update-sensors"><i class="oicon-ok oicon-white"></i> Save Changes</button>
  </div>
</form>
