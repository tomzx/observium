<?php

if ($_POST['editing'])
{
  if ($_SESSION['userlevel'] > "7")
  {
    $override_sysContact_bool = mres($_POST['override_sysContact']);
    if (isset($_POST['sysContact'])) { $override_sysContact_string  = mres($_POST['sysContact']); }
    $disable_notify  = mres($_POST['disable_notify']);

    if ($override_sysContact_bool) { set_dev_attrib($device, 'override_sysContact_bool', '1'); } else { del_dev_attrib($device, 'override_sysContact_bool'); }
    if (isset($override_sysContact_string)) { set_dev_attrib($device, 'override_sysContact_string', $override_sysContact_string); };
    if ($disable_notify) { set_dev_attrib($device, 'disable_notify', '1'); } else { del_dev_attrib($device, 'disable_notify'); }

    $update_message = "Device alert settings updated.";
    $updated = 1;
  }
  else
  {
    include("includes/error-no-perm.inc.php");
  }
}

if ($updated && $update_message)
{
  print_message($update_message);
} elseif ($update_message) {
  print_error($update_message);
}

$override_sysContact_bool = get_dev_attrib($device,'override_sysContact_bool');
$override_sysContact_string = get_dev_attrib($device,'override_sysContact_string');
$disable_notify = get_dev_attrib($device,'disable_notify');
?>

      <form id="edit" name="edit" method="post" action="" class="form-horizontal">
        <input type="hidden" name="editing" value="yes">
        <fieldset>
          <legend>Alert Settings</legend>

  <div class="control-group">
    <label class="control-label" for="override_sysContact">Override sysContact</label>
    <div class="controls">
      <input onclick="edit.sysContact.disabled=!edit.override_sysContact.checked" type="checkbox"
            name="override_sysContact" <?php if ($override_sysContact_bool) { echo(' checked="1"'); } ?> />
      <span class="help-inline">Use custom contact below</span>
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="sysContact">Custom contact</label>
    <div class="controls">
      <input type=text name="sysContact" size="32" <?php if (!$override_sysContact_bool) { echo(' disabled="1"'); } ?> value="<?php echo($override_sysContact_string); ?>" />
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="override_sysContact">Disable alerts</label>
    <div class="controls">
      <input type="checkbox" name="disable_notify"<?php if ($disable_notify) { echo(' checked="1"'); } ?> />
      <span class="help-inline">Don't send alert mails (<i>but write to eventlog</i>)</span>
    </div>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="icon-ok icon-white"></i> Save Changes</button>
  </div>
</form>

