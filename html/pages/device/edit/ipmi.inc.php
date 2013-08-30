<?php

if ($_POST['editing'])
{
  if ($_SESSION['userlevel'] > "7")
  {
    $ipmi_hostname = mres($_POST['ipmi_hostname']);
    $ipmi_username = mres($_POST['ipmi_username']);
    $ipmi_password = mres($_POST['ipmi_password']);

    if ($ipmi_hostname != '') { set_dev_attrib($device, 'ipmi_hostname', $ipmi_hostname); } else { del_dev_attrib($device, 'ipmi_hostname'); }
    if ($ipmi_username != '') { set_dev_attrib($device, 'ipmi_username', $ipmi_username); } else { del_dev_attrib($device, 'ipmi_username'); }
    if ($ipmi_password != '') { set_dev_attrib($device, 'ipmi_password', $ipmi_password); } else { del_dev_attrib($device, 'ipmi_password'); }

    $update_message = "Device IPMI data updated.";
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

?>

<form id="edit" name="edit" method="post" action="" class="form-horizontal">
  <fieldset>
  <legend>IPMI Settings</legend>
  <input type=hidden name="editing" value="yes">

  <div class="control-group">
    <label class="control-label" for="ipmi_hostname">IPMI/BMC Hostname</label>
    <div class="controls">
      <input name="ipmi_hostname" type="text" size="32" value="<?php echo(get_dev_attrib($device,'ipmi_hostname')); ?>"></input>
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="ipmi_username">IPMI/BMC Username</label>
    <div class="controls">
      <input name="ipmi_username" type="text" size="32" value="<?php echo(get_dev_attrib($device,'ipmi_username')); ?>"></input>
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="ipmi_password">IPMI/BMC Password</label>
    <div class="controls">
      <input name="ipmi_password" type="password" size="32" value="<?php echo(get_dev_attrib($device,'ipmi_password')); ?>"></input>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="icon-ok icon-white"></i> Save Changes</button>
    <span class="help-inline">To disable IPMI polling, please clear the setting fields and click <strong>Save Changes</strong>.</span>
  </div>

  </fieldset>
</form>
