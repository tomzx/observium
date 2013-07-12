<?php

if ($_SESSION['userlevel'] < 10)
{
  include("includes/error-no-perm.inc.php");

  exit;
}

$pagetitle[] = "Delete device";

if (is_numeric($_REQUEST['id']))
{
  if ($_REQUEST['confirm'])
  {
    $delete_rrd = ($_REQUEST['deleterrd'] == 'confirm') ? TRUE : FALSE;
    print_success(delete_device(mres($_REQUEST['id']), $delete_rrd));
  }
  else
  {
    $device = device_by_id_cache($_REQUEST['id']);
    print_warning("Are you sure you want to delete device <strong>" . $device['hostname'] . "</strong>?");
?>
<br />
<form name="form1" method="post" action="" class="form-horizontal" >
  <input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
  <input type="hidden" name="confirm" value="1" />
  <!--<input type="submit" class="submit" name="Submit" value="Confirm host deletion" />-->
  <button type="submit" class="btn btn-danger"><i class="icon-remove icon-white"></i> Delete Device</button>
</form>

<?php
  }
}
else
{
?>

<form name="form1" method="post" action="" class="form-horizontal" >

  <script type="text/javascript">
    function showWarning(checked) {
      $('#warning').toggle();
      if (checked) {
        $('#deleteBtn').removeAttr('disabled');
      } else {
        $('#deleteBtn').attr('disabled', 'disabled');
      }
    }
    function showWarningRRD(checked) {
      if (checked) {
        $('.alert').hide();
      } else {
        $('.alert').show();
      }
    }
  </script>

  <fieldset>
    <legend>Delete device</legend>
<?php
  print_warning("<h4>Warning!</h4>
      This will delete this device from Observium including all logging entries, but will not delete the RRDs.");
?>

    <div class="control-group">
      <label class="control-label" for="id">Device</label>
      <div class="controls">
        <select name="id">
<?php
foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $data)
{
  $disabled = ($data['disabled']) ? ' [disabled]' : '';
  echo("<option value='".$data['device_id']."'>".$data['hostname'].$disabled."</option>");
}
?>
        </select>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label">Delete RRDs</label>
      <div class="controls">
        <input type="checkbox" name="deleterrd" value="confirm" onchange="javascript: showWarningRRD(this.checked);">
      </div>
    </div>
    
    <div class="control-group">
      <label class="control-label" for="id">Confirm Deletion</label>
      <div class="controls">
        <input type="checkbox" name="confirm" value="confirm" onchange="javascript: showWarning(this.checked);">
      </div>
    </div>
  </fieldset>

  <div class="form-actions">
    <button id="deleteBtn" type="submit" class="btn btn-danger" disabled="disabled"><i class="icon-remove icon-white"></i> Delete Device</button>
  </div>

</form>
<?php
}
?>
