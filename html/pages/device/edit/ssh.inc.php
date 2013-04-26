<?php

if ($_POST['editing'])
{
  if ($_SESSION['userlevel'] > "7")
  {
    $ssh_port = mres($_POST['ssh_port']);

    #FIXME needs better feedback
    $update = array(
      'ssh_port' => $ssh_port
    );

    $rows_updated = dbUpdate($update, 'devices', '`device_id` = ?',array($device['device_id']));

    if ($rows_updated > 0)
    {
      $update_message = $rows_updated . " Device record updated.";
      $updated = 1;
    } elseif ($rows_updated = '-1') {
      $update_message = "Device record unchanged. No update necessary.";
      $updated = -1;
    } else {
      $update_message = "Device record update error.";
      $updated = 0;
    }
  }
}

$device = dbFetchRow("SELECT * FROM `devices` WHERE `device_id` = ?", array($device['device_id']));
$descr  = $device['purpose'];

if ($updated && $update_message)
{
  print_message($update_message);
} elseif ($update_message) {
  print_error($update_message);
}

?>

<form id="edit" name="edit" method="post" class="form-horizontal" action="">
  <input type=hidden name="editing" value="yes">

  <!-- To be able to hide it -->
  <div id="ssh">
    <fieldset>
      <legend>SSH Connectivity</legend>
      <div class="control-group">
        <label class="control-label" for="ssh_port">SSH Port</label>
        <div class="controls">
          <input type=text name="ssh_port" size="32" value="<?php echo $device['ssh_port']; ?>"/>
        </div>
      </div>
    </fieldset>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="oicon-ok oicon-white"></i> Save Changes</button>
  </div>

</form>
