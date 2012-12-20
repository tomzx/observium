<?php

if ($_POST['ignoreport'])
{
  if ($_SESSION['userlevel'] == '10')
  {
    include("includes/port-edit.inc.php");
  }
}

if ($updated && $update_message)
{
  print_message($update_message);
} elseif ($update_message) {
  print_error($update_message);
}

?>

<form id='ignoreport' name='ignoreport' method='post' action='' class='form form-inline'>
  <input type=hidden name='ignoreport' value='yes'>
  <input type=hidden name=device value='<?php echo $device['device_id']; ?>'>

<table class='table table-striped table-bordered table-condensed table-rounded'>
  <thead>
    <tr align=center>
      <th width=100>ifIndex</th>
      <th width=100>ifAlias</th>
      <th width=100>ifType</th>
      <th width=50>Admin</th>
      <th width=110>Oper</th>
      <th width=110>Disable</th>
      <th width=110>Ignore</th>
      <th>Description</th>
    </tr>
    <tr align=center>
      <th><button class='btn btn-mini btn-primary' type='submit' value='Save' title='Save current port disable/ignore settings'> Save </button></td>
      <th><button class='btn btn-mini btn-danger' type='submit' value='Reset' id='form-reset' title='Reset form to previously-saved settings'> Reset </button></th>
      <th></th>
      <th></th>
      <th><button class='btn btn-mini' type='submit' value='Alerted' id='alerted-toggle' title='Toggle alerting on all currently-alerted ports'>Alerted</button>
          <button class='btn btn-mini' type='submit' value='Down' id='down-select' title='Disable alerting on all currently-down ports'>Down</button></th>
      <th><button class='btn btn-mini' type='submit' value='Toggle' id='disable-toggle' title='Toggle polling for all ports'>Toggle</button>
          <button class='btn btn-mini' type='submit' value='Select' id='disable-select' title='Disable polling on all ports'>Select</button></th>
      <th><button class='btn btn-mini' type='submit' value='Toggle' id='ignore-toggle' title='Toggle alerting for all ports'>Toggle</button>
          <button class='btn btn-mini' type='submit' value='Select' id='ignore-select' title='Disable alerting on all ports'>Select</button></th>
      <th></th>
    </tr>
  </thead>

<script>
$(document).ready(function() {
    $('#disable-toggle').click(function(event) {
        // invert selection on all disable buttons
        event.preventDefault();
        $('[name^="disabled_"]').check('toggle');
    });
    $('#ignore-toggle').click(function(event) {
        // invert selection on all ignore buttons
        event.preventDefault();
        $('[name^="ignore_"]').check('toggle');
    });
    $('#disable-select').click(function(event) {
        // select all disable buttons
        event.preventDefault();
        $('[name^="disabled_"]').check();
    });
    $('#ignore-select').click(function(event) {
        // select all ignore buttons
        event.preventDefault();
        $('[name^="ignore_"]').check();
    });
    $('#down-select').click(function(event) {
        // select ignore buttons for all ports which are down
        event.preventDefault();
        $('[name^="operstatus_"]').each(function() {
            var name = $(this).attr('name');
            var text = $(this).text();
            if (name && text == 'down') {
                // get the interface number from the object name
                var port_id = name.split('_')[1];
                // find its corresponding checkbox and toggle it
                $('[name="ignore_' + port_id + '"]').check();
            }
        });
    });
    $('#alerted-toggle').click(function(event) {
        // toggle ignore buttons for all ports which are in class red
        event.preventDefault();
        $('.red').each(function() {
            var name = $(this).attr('name');
            if (name) {
                // get the interface number from the object name
                var port_id = name.split('_')[1];
                // find its corresponding checkbox and toggle it
                $('[name="ignore_' + port_id + '"]').check('toggle');
            }
        });
    });
    $('#form-reset').click(function(event) {
        // reset objects in the form to their previous values
        event.preventDefault();
        $('#ignoreport')[0].reset();
    });
});
</script>

<?php

foreach (dbFetchRows("SELECT * FROM `ports` WHERE `device_id` = ? ORDER BY `ifIndex` ", array($device['device_id'])) as $port)
{
  $port = ifLabel($port);

  echo("<tr>");
  echo("<td align=center>". $port['ifIndex']."</td>");
  echo("<td align=left>".$port['label'] . "</td>");
  echo("<td align=left>".fixiftype($port['ifType']) . "</td>");
  echo("<td align=right>". $port['ifAdminStatus']."</td>");

  # Mark interfaces which are OperDown (but not AdminDown) yet not ignored or disabled, or up yet ignored or disabled
  # - as to draw the attention to a possible problem.
  $isportbad = ($port['ifOperStatus'] == 'down' && $port['ifAdminStatus'] != 'down') ? 1 : 0;
  $dowecare  = ($port['ignore'] == 0 && $port['disabled'] == 0) ? $isportbad : !$isportbad;
  $outofsync = $dowecare ? " class='red'" : "";

  echo("<td align=right><span name='operstatus_".$port['port_id']."'".$outofsync.">". $port['ifOperStatus']."</span></td>");

  echo("<td align=center>");
  echo("<input type=checkbox name='disabled_".$port['port_id']."'".($port['disabled'] ? 'checked' : '').">");
  echo("<input type=hidden name='olddis_".$port['port_id']."' value=".($port['disabled'] ? 1 : 0).">");
  echo("</td>");

  echo("<td align=center>");
  echo("<input type=checkbox name='ignore_".$port['port_id']."'".($port['ignore'] ? 'checked' : '').">");
  echo("<input type=hidden name='oldign_".$port['port_id']."' value=".($port['ignore'] ? 1 : 0).">");
  echo("</td>");
  echo("<td align=left>".$port['ifAlias'] . "</td>");

  echo("</tr>\n");

  $row++;
}

echo('</table>');
echo('</form>');

?>
