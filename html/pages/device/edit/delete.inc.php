
<form id="delete_host" name="delete_host" method="post" action="delhost/"  class="form-horizontal">
  <input type="hidden" name="id" value="<?php echo($device['device_id']); ?>">

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
    <legend>Delete device</legend>
    <div class="alert alert-error">
      <h4>Warning!</h4>
      This will delete this device from Observium including all logging entries, but will not delete the RRDs.
    </div>

    <div class="control-group">
      <label class="control-label" for="sysContact">Confirm Deletion</label>
      <div class="controls">
        <input type="checkbox" name="confirm" value="confirm" onchange="javascript: showWarning(this.checked);">
      </div>
    </div>

    <div class="form-actions">
      <button id="deleteBtn" type="submit" class="btn btn-danger" name="delete" disabled="disabled"><i class="icon-remove icon-white"></i> Delete device</button>
    </div>
  </fieldset>
</form>


