<?php

# Load our list of available applications
if ($handle = opendir($config['install_dir'] . "/includes/polling/applications/"))
{
  while (false !== ($file = readdir($handle)))
  {
    if ($file != "." && $file != ".." && strstr($file, ".inc.php"))
    {
      $applications[] = str_replace(".inc.php", "", $file);
    }
  }
  closedir($handle);
}

# Check if the form was POSTed
if ($_POST['device'])
{
  $updated = 0;
  $param[] = $device['device_id'];
  foreach (array_keys($_POST) as $key)
  {
    if (substr($key,0,4) == 'app_')
    {
      $param[] = substr($key,4);
      $enabled[] = substr($key,4);
      $replace[] = "?";
    }
  }

  if (count($enabled)) {
    $updated += dbDelete('applications', "`device_id` = ? AND `app_type` NOT IN (".implode(",",$replace).")", $param);
  } else {
    $updated += dbDelete('applications', "`device_id` = ?", array($param));
  }

  foreach (dbFetchRows( "SELECT `app_type` FROM `applications` WHERE `device_id` = ?", array($device['device_id'])) as $row)
  {
    $app_in_db[] = $row['app_type'];
  }

  foreach ($enabled as $app)
  {
    if (!in_array($app,$app_in_db))
    {
      $updated += dbInsert(array('device_id' => $device['device_id'], 'app_type' => $app), 'applications');
    }
  }

  if ($updated)
  {
    print_message("Applications updated!");
  }
  else
  {
    print_message("No changes.");
  }
}

# Show list of apps with checkboxes

$apps_enabled = dbFetchRows("SELECT * from `applications` WHERE `device_id` = ? ORDER BY app_type", array($device['device_id']));
if (count($apps_enabled))
{
  foreach ($apps_enabled as $application)
  {
    $app_enabled[] = $application['app_type'];
  }
}
?>

<form id='appedit' name='appedit' method='post' action='' class='form-inline'>
  <fieldset>
  <legend>Device Properties</legend>

  <input type=hidden name=device value='<?php echo $device['device_id'];?>'>
<table class='table table-striped table-bordered table-condensed table-rounded'>
  <thead>
    <tr align=center>
      <th width=100>Enable</th>
      <th>Application</th>
    </tr>
  </thead>
  <tbody>

<?php

foreach ($applications as $app)
{
  echo("    <tr>");
  echo("      <td align=center>");
  echo("      <div class='switch switch-mini' data-on='primary' data-off='danger' data-on-label='Yes' data-off-label='No'>
                <input type=checkbox ". (in_array($app,$app_enabled) ? ' checked="1"' : '') . " name='app_". $app ."'></div>");
  echo("      </td>");
  echo("      <td align=left>". nicecase($app) . "</td>");
  echo("    </tr>
");

  $row++;
}
?>

</table>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="oicon-ok oicon-white"></i> Save Changes</button>
  </div>

</form>
