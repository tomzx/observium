<?php

if($_POST['toggle_poller'] && isset($config['poller_modules'][$_POST['toggle_poller']]))
{
  $module = mres($_POST['toggle_poller']);
  if (isset($attribs['poll_'.$module]) && $attribs['poll_'.$module] != $config['poller_modules'][$_POST['toggle_poller']])
  {
    del_dev_attrib($device, 'poll_' . $module);
  } elseif ($config['poller_modules'][$_POST['toggle_poller']] == 0) {
    set_dev_attrib($device, 'poll_' . $module, "1");
  } else {
    set_dev_attrib($device, 'poll_' . $module, "0");
  }
  $attribs = get_dev_attribs($device['device_id']);
}

if($_POST['toggle_discovery'] && isset($config['discovery_modules'][$_POST['toggle_discovery']]))
{
  $module = mres($_POST['toggle_discovery']);
  if (isset($attribs['discover_'.$module]) && $attribs['discover_'.$module] != $config['discovery_modules'][$_POST['toggle_discovery']])
  {
    del_dev_attrib($device, 'discover_' . $module);
  } elseif ($config['discovery_modules'][$_POST['toggle_discovery']] == 0) {
    set_dev_attrib($device, 'discover_' . $module, "1");
  } else {
    set_dev_attrib($device, 'discover_' . $module, "0");
  }
  $attribs = get_dev_attribs($device['device_id']);
}
?>

<div class="row">
      <div class="col-md-6">

<fieldset>
  <legend>Poller Modules</legend>
</fieldset>

<table class="table table-bordered table-striped table-condensed table-rounded">
  <thead>
    <tr>
      <th>Module</th>
      <th width="80">Global</th>
      <th width="80">Device</th>
      <th width="80"></th>
    </tr>
  </thead>
  <tbody>

<?php
foreach ($config['poller_modules'] as $module => $module_status)
{

  echo('<tr><td><b>'.$module.'</b></td><td>');

  echo(($module_status ? '<span class=green>enabled</span>' : '<span class=red>disabled</span>' ));

  echo('</td><td>');

  if (isset($attribs['poll_'.$module]))
  {
    if ($attribs['poll_'.$module]) { echo("<span class=green>enabled</span>"); $toggle = "Disable"; $btn_class = "btn-danger";
    } else { echo('<span class=red>disabled</span>'); $toggle = "Enable"; $btn_class = "btn-success";}
  } else {
    if ($module_status) { echo("<span class=green>enabled</span>"); $toggle = "Disable"; $btn_class = "btn-danger";
    } else { echo('<span class=red>disabled</span>'); $toggle = "Enable"; $btn_class = "btn-success";}
  }

  echo('</td><td>');

  echo('<form id="toggle_poller" name="toggle_poller" method="post" action="">
  <input type=hidden name="toggle_poller" value="'.$module.'">
  <button type="submit" class="btn btn-mini '.$btn_class.'" name="Submit" value="Toggle">'.$toggle.'</button>
  </label>
</form>');

  echo('</td></tr>');
  $i++;
}
?>

</table>

      </div>
      <div class="col-md-6">

<fieldset>
  <legend>Discovery Modules</legend>
</fieldset>

<table class="table table-bordered table-striped table-condensed table-rounded">
  <thead>
    <tr>
      <th>Module</th>
      <th width="80">Global</th>
      <th width="80">Device</th>
      <th width="80"></th>
    </tr>
  </thead>
  <tbody>

<?php
foreach ($config['discovery_modules'] as $module => $module_status)
{
  if (!is_integer($i/2)) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }
  echo('<tr bgcolor="'.$bg_colour.'"><td><b>'.$module.'</b></td><td>');

  echo(($module_status ? '<span class=green>enabled</span>' : '<span class=red>disabled</span>' ));

  echo('</td><td>');

  if (isset($attribs['discover_'.$module]))
  {
    if ($attribs['discover_'.$module]) { echo("<span class=green>enabled</span>"); $toggle = "Disable"; $btn_class = "btn-danger";
    } else { echo('<span class=red>disabled</span>'); $toggle = "Enable"; $btn_class = "btn-success";}
  } else {
    if ($module_status) { echo("<span class=green>enabled</span>"); $toggle = "Disable"; $btn_class = "btn-danger";
    } else { echo('<span class=red>disabled</span>'); $toggle = "Enable"; $btn_class = "btn-success";}
  }

  echo('</td><td>');

  echo('<form id="toggle_discovery" name="toggle_discovery" method="post" action="">
  <input type=hidden name="toggle_discovery" value="'.$module.'">
  <button type="submit" class="btn btn-mini '.$btn_class.'" name="Submit" value="Toggle">'.$toggle.'</button>
  </label>
</form>');

  echo('</td></tr>');

  $i++;
}
echo('</table>

</div>
    </div>
</div>');

?>
