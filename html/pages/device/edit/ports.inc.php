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
<fieldset>
  <legend>Port Properties</legend>

  <input type=hidden name='ignoreport' value='yes'>
  <input type=hidden name=device value='<?php echo $device['device_id']; ?>'>

<table class='table table-striped table-bordered table-condensed table-rounded'>
  <thead>
    <tr align=center>
      <th width=70>ifIndex</th>
      <th width=200>Port</th>
      <th width=145>ifType</th>
      <th width=110>Polling</th>
      <th width=110>Alerts</th>
      <!-- <th width=110>% Threshold</th>   -->
      <!-- <th width=110>BPS Threshold</th> -->
      <!-- <th width=110>PPS Threshold</th> -->
      <th width=110>64bit</th>
    </tr>
    <tr align=center>
      <th><button class='btn btn-mini btn-primary' type='submit' value='Save' title='Save current port disable/ignore settings'><i class="icon-ok icon-white"></i> Save</button></td>
      <th><!-- <button class='btn btn-mini btn-danger' type='submit' value='Reset' id='form-reset' title='Reset form to previously-saved settings'><i class="oicon-remove oicon-white"></i> Reset</button> --></th>
      <th><button class='btn btn-mini' type='submit' value='Alerted' id='alerted-toggle' title='Toggle alerting on all currently-alerted ports'>Alerted</button>
          <button class='btn btn-mini' type='submit' value='Down' id='down-select' title='Disable alerting on all currently-down ports'>Down</button></th>
      <th><button class='btn btn-mini' type='submit' value='Toggle' id='disable-toggle' title='Toggle polling for all ports'>Toggle</button>
          <button class='btn btn-mini' type='submit' value='Select' id='disable-select' title='Disable polling on all ports'>All</button></th>
      <th><button class='btn btn-mini' type='submit' value='Toggle' id='ignore-toggle' title='Toggle alerting for all ports'>Toggle</button>
          <button class='btn btn-mini' type='submit' value='Select' id='ignore-select' title='Disable alerting on all ports'>All</button></th>
      <th></th>

<!--      <th></th>
      <th></th>
      <td></th> -->
    </tr>
  </thead>

<script>
$(document).ready(function() {
    $('#disable-toggle').click(function(event) {
        // invert selection on all disable buttons
        event.preventDefault();
        $('[id^="disabled_"]').each(function() {
            var id = $(this).attr('id');
            // get the interface number from the object name
            var port_id = id.split('_')[1];
            // find its corresponding checkbox and toggle it
            $('[id="disabled_' + port_id + '"]').switch('toggleState');
        });
    });

    $('#ignore-toggle').click(function(event) {
        // invert selection on all ignore buttons
        event.preventDefault();
        $('[id^="ignore_"]').each(function() {
            var id = $(this).attr('id');
            // get the interface number from the object name
            var port_id = id.split('_')[1];
            // find its corresponding checkbox and toggle it
            $('[id="ignore_' + port_id + '"]').switch('toggleState');
        });
    });
    $('#disable-select').click(function(event) {
        // select all disable buttons
        event.preventDefault();
        $('[id^="disabled_"]').switch('setState', true);
    });
    $('#ignore-select').click(function(event) {
        // select all ignore buttons
        event.preventDefault();
        $('[id^="ignore_"]').switch('setState', true);;
    });

    $('#down-select').click(function(event) {
        // select ignore buttons for all ports which are down
        event.preventDefault();
        $('[name^="operstatus_"]').each(function() {
            var name = $(this).attr('name');
            var text = $(this).text();
            if (name && text == 'down' || name && text == 'lowerLayerDown') {
                // get the interface number from the object name
                var port_id = name.split('_')[1];
                // find its corresponding checkbox and toggle it
                $('[id="ignore_' + port_id + '"]').switch('setState', true);
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
                $('[id="ignore_' + port_id + '"]').switch('setState', true);
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
  humanize_port($port);

  echo("<tr>");
  echo("<td align=center>". $port['ifIndex']."</td>");
  echo("<td align=left>".$port['label']."<br />".$port['ifAlias']."</td>");

  $enabled = ($port['ifAdminStatus'] == 'up') ? " class='green'" : "";

  echo("<td align=left>".fixiftype($port['ifType']) . "<br />");

  # Mark interfaces which are OperDown (but not AdminDown) yet not ignored or disabled, or up yet ignored or disabled
  # - as to draw the attention to a possible problem.
  $isportbad = ($port['ifOperStatus'] != 'up' && $port['ifAdminStatus'] != 'down') ? 1 : 0;
  $dowecare  = ($port['ignore'] == 0 && $port['disabled'] == 0) ? $isportbad : !$isportbad;
  $outofsync = $dowecare ? " class='red'" : " class='green'";

  echo("<span $enabled>".$port['ifAdminStatus']."</span> / <span name='operstatus_".$port['port_id']."'".$outofsync.">". $port['ifOperStatus']."</span></td>");

  echo('<td align=center style="vertical-align: middle;">');
  echo("<div id='disabled_".$port['port_id']."' class='switch switch-mini' data-on='danger' data-off='primary' data-on-label='No' data-off-label='Yes'><input type=checkbox name='disabled_".$port['port_id']."'".($port['disabled'] ? 'checked' : '')."></div>");
  echo("<input type=hidden name='olddis_".$port['port_id']."' value=".($port['disabled'] ? 1 : 0).">");
  echo("</td>");

  echo('<td align=center style="vertical-align: middle;">');
  echo("<div id='ignore_".$port['port_id']."' class='switch switch-mini' data-on='danger' data-off='primary' data-on-label='No' data-off-label='Yes'><input type=checkbox name='ignore_".$port['port_id']."'".($port['ignore'] ? 'checked' : '')."></div>");
  echo("<input type=hidden name='oldign_".$port['port_id']."' value=".($port['ignore'] ? 1 : 0).">");
  echo("</td>");

  ?>

  <?php
#  echo('<td>  <input class="input-mini" name="threshold_perc_in-'.$port['port_id'].'" size="3" value="'.$port['threshold_perc_in'].'"></input>');
#  echo('<br /><input class="input-mini" name="threshold_perc_out-'.$port['port_id'].'" size="3" value="'.$port['threshold_perc_out'].'"></input></td>');
#  echo('<td>  <input class="input-mini" name="threshold_bps_in-'.$port['port_id'].'" size="3" value="'.$port['threshold_bps_in'].'"></input>');
#  echo('<br /><input class="input-mini" name="threshold_bps_out-'.$port['port_id'].'" size="3" value="'.$port['threshold_bps_out'].'"></input></td>');
#  echo('<td>  <input class="input-mini" name="threshold_pps_in-'.$port['port_id'].'" size="3" value="'.$port['threshold_pps_in'].'"></input>');
#  echo('<br /><input class="input-mini" name="threshold_pps_out-'.$port['port_id'].'" size="3" value="'.$port['threshold_pps_out'].'"></input></td>');

  if($port['port_64bit'] == 1)
  {
    echo '<td class="green">Yes</td>';
  } elseif($port['port_64bit'] == 0) {
    echo '<td class="orange">No</td>';
  } else {
    echo '<td>Unchecked</td>';
  }

  echo '</tr>'.PHP_EOL;

  $row++;
}
?>
</table>
</fieldset>
  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="icon-ok icon-white"></i> Save Changes</button>
  </div>
</form>
