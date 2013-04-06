<h2>Delete Device</h2>

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
    print_message(delete_device(mres($_REQUEST['id'])));
  }
  else
  {
    $device = device_by_id_cache($_REQUEST['id']);
    print_message("Are you sure you want to delete device " . $device['hostname'] . "?");
?>
<br />
<form name="form1" method="post" action="" class="form-horizontal" >
    <input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
    <input type="hidden" name="confirm" value="1" />
    <input type="submit" class="submit" name="Submit" value="Confirm host deletion" />

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
      </script>

  <fieldset>
    <legend>Device selection</legend>
    <div class="control-group">
      <label class="control-label" for="id">Device</label>
      <div class="controls">
        <select name="id">

<?php

foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $data)
{
  echo("<option value='".$data['device_id']."'>".$data['hostname']."</option>");
}

?>

        </select>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" for="id">Confirm removal</label>
      <div class="controls">
        <input type="checkbox" name="confirm" value="confirm" onchange="javascript: showWarning(this.checked);">
      </div>
    </div>
  </fieldset>

  <div class="form-actions">
    <button id="deleteBtn" type="submit" class="btn btn-danger" disabled="disabled"><i class="oicon-remove oicon-white"></i> Remove Device</button>
  </div>

</form>
<?php
}
?>
