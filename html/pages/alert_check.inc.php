<?php

/**
 * Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Adam Armstrong - http://www.observium.org
 *
 * @package    observium
 * @subpackage webui
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

// Alert test display and editing page.

$check = dbFetchRow("SELECT * FROM `alert_tests` WHERE `alert_test_id` = ?", array($vars['alert_test_id']));

if(($_SESSION['userlevel'] < 10))
{
 // No editing for you!
}
elseif($vars['editing'])
{
  // We are editing. Lets see what we are editing.
  if($vars['editing'] == "check_conditions")
  {
    $conds = array(); $cond_array = array();
    foreach(explode("\n", $vars['check_conditions']) AS $cond)
    {
      list($cond_array['metric'], $cond_array['condition'], $cond_array['value']) = explode(" ", $cond);
      $conds[] = $cond_array;
    }
    $conds = json_encode($conds);
    $rows_updated = dbUpdate(array('conditions' => $conds), 'alert_tests', '`alert_test_id` = ?',array($vars['alert_test_id']));
  }
  elseif($vars['editing'] == "assoc_conditions")
  {
    $d_conds = array(); $cond_array = array();
    foreach(explode("\n", trim($vars['assoc_device_conditions'])) AS $cond)
    {
      list($cond_array['attrib'], $cond_array['condition'], $cond_array['value']) = explode(" ", $cond);
      $d_conds[] = $cond_array;
    }
    $d_conds = json_encode($d_conds);

    $e_conds = array(); $cond_array = array();
    foreach(explode("\n", trim($vars['assoc_entity_conditions'])) AS $cond)
    {
      list($cond_array['attrib'], $cond_array['condition'], $cond_array['value']) = explode(" ", $cond);
      $e_conds[] = $cond_array;
    }
    $e_conds = json_encode($e_conds);
    $rows_updated = dbUpdate(array('device_attributes' => $d_conds, 'attributes' => $e_conds), 'alert_assoc', '`alert_assoc_id` = ?', array($vars['assoc_id']));

  }


  if ($rows_updated > 0)
  {
    $update_message = $rows_updated . " Record(s) updated.";
    $updated = 1;
  } elseif ($rows_updated = '-1') {
    $update_message = "Record unchanged. No update necessary.";
    $updated = -1;
  } else {
    $update_message = "Record update error.";
    $updated = 0;
  }

  if ($updated && $update_message)
  {
    print_message($update_message);
  } elseif ($update_message) {
    print_error($update_message);
  }

  // Refresh the $check array to reflect the updates
  $check = dbFetchRow("SELECT * FROM `alert_tests` WHERE `alert_test_id` = ?", array($vars['alert_test_id']));

}

// Process the alert checker to add classes and colours and count status.
humanize_alert_check($check);

/// End bit to go in to function

?>

<div class="row">
  <div class="col-md-12">
    <div class="well info_box">
      <div class="title"><i class="oicon-bell"></i> Checker Details</div>
      <div class="title" style="float: right; margin-bottom: -10px;"><a href=""><a href="#delete_alert_modal" data-toggle="modal"><i class="oicon-minus-circle"></i> Delete</a></div>

			<?php
#if ($_SESSION['userlevel'] >= '10') { echo '      <div class="title pull-right"><a href="'.generate_url($vars, array('edit' => "TRUE")).'"><i class="oicon-gear"></i> Edit</a></div>'; }

echo '
      <div class="content">

        <table class="table table-striped table-bordered table-condensed table-rounded">
         <thead>
          <tr>
            <th style="width: 5%;">Test ID</th>
            <th style="width: 15%;">Entity Type</th>
            <th style="width: 20%;">Name</th>
            <th style="width: 50%;">Message</th>
            <th style="width: 10%;">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>', $check['alert_test_id'], '</strong></td>
            <td><strong>', nicecase($check['entity_type']), '</strong></td>
            <td><strong>', $check['alert_name'], '</strong></td>
            <td><i>', $check['alert_message'], '</i></td>
            <td><i>', $check['status_numbers'], '</i></td>
          </tr>
        </tbody></table>
      </div>
    </div>
  </div>
</div>';

$assocs = dbFetchRows("SELECT * FROM `alert_assoc` WHERE `alert_test_id` = ?", array($vars['alert_test_id']));

echo '
<div class="row">';

echo '
  <div class="col-md-4">
    <div class="well info_box">
      <div class="title"><i class="oicon-traffic-light"></i> Check Conditions</div>
      <div class="title" style="float: right; margin-bottom: -13px; margin-top: -2px;"><a href="#conditions_modal" data-toggle="modal"><i class="oicon-pencil"></i> Edit</a></div>
      <div class="content">';

      if($check['and'] == "1")
			{
			  echo 'Requires all conditions to match';
			} else {
			  echo 'Requires any condition to match';
			}

   echo('<table class="table table-condensed table-bordered table-striped table-rounded">');
   echo('<thead><tr>');
   echo('<th style="width: 33%;">Metric</th>');
   echo('<th style="width: 33%;">Condition</th>');
   if ($_SESSION['userlevel'] >= '10') {
     echo '<th style="width: 33%;">Value</th>';
   } else {
     echo '<th style="width: 33%;">Value</th>';
   }
   echo '</tr></thead>';


  $conditions = json_decode($check['conditions'], TRUE);

  $condition_text = array();

  foreach($conditions as $condition)
  {
    $condition_text[] = $condition['metric'].' '.$condition['condition'].' '.$condition['value'];
    echo '<tr>';
    echo '<td>'.$condition['metric'].'</td>';
    echo '<td>'.$condition['condition'].'</td>';
    echo '<td>'.$condition['value'].'</td>';
    echo '</tr>';
  }

    echo('</table>');


echo '
      </div>
    </div>
  </div>';

echo '
  <div class="col-md-8">
    <div class="well info_box">
      <div class="title"><i class="oicon-sql-join-left"></i> Associations</div>
      <div class="content">';

    echo '<table class="table table-condensed table-bordered table-striped table-rounded">';
    echo '<thead><tr>';
    echo '<th style="width: 45%;">Device Match</th>';
    echo '<th style="">Entity Match</th>';
    echo '<th style="width: 10%;"></th>';
    echo '</tr></thead>';


    foreach($assocs as $assoc_id => $assoc)
    {
      echo('<tr>');
      echo('<td>');
      echo('<strong>');
      $assoc['device_attributes'] = json_decode($assoc['device_attributes'], TRUE);
      $assoc_dev_text = array();
      if(is_array($assoc['device_attributes']))
      {
        foreach($assoc['device_attributes'] as $attribute)
        {
          echo($attribute['attrib'].' ');
          echo($attribute['condition'].' ');
          echo($attribute['value']);
          echo('<br />');
          $assoc_dev_text[] = $attribute['attrib'].' '.$attribute['condition'].' '.$attribute['value'];
        }
      } else {
        echo("*");
      }
      echo("</strong><i>");
      echo('</td>');
      echo('<td><strong>');
      $assoc['attributes'] = json_decode($assoc['attributes'], TRUE);
      $assoc_entity_text = array();
      if(is_array($assoc['attributes']))
      {
        foreach($assoc['attributes'] as $attribute)
        {
          echo($attribute['attrib'].' ');
          echo($attribute['condition'].' ');
          echo($attribute['value']);
          echo('<br />');
          $assoc_entity_text[] = $attribute['attrib'].' '.$attribute['condition'].' '.$attribute['value'];
        }
      } else {
        echo("*");
      }
      echo '</td>';
      echo '<td><a href="#assoc_modal_',$assoc['alert_assoc_id'],'" data-toggle="modal"><i class="oicon-pencil"></i> Edit</a></td>';

?>

<div id="assoc_modal_<?php echo $assoc['alert_assoc_id']; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <form id="edit" name="edit" method="post" class="form" action="">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><i class="oicon-sql-join-inner"></i> Edit Association Conditions</h3>
  </div>
  <div class="modal-body">

  <input type=hidden name="editing" value="assoc_conditions">
  <input type=hidden name="assoc_id" value = "<?php echo $assoc['alert_assoc_id']; ?>">
  <span class="help-block">Please exercise care when editing here.</span>

  <fieldset>
    <div class="control-group">
      <label>Device match</label>
      <div class="controls">
        <textarea class="col-md-12" rows="4" name="assoc_device_conditions"><?php echo(htmlentities(implode("\n", $assoc_dev_text))); ?></textarea>
      </div>
    </div>

    <div class="control-group">
      <label>Entity match</label>
      <div class="controls">
        <textarea class="col-md-12" rows="4" name="assoc_entity_conditions"><?php echo(htmlentities(implode("\n", $assoc_entity_text))); ?></textarea>
      </div>
    </div>
  </fieldset>

  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="icon-ok icon-white"></i> Save Changes</button>
  </div>
 </form>
</div>

<?php


    }
    echo '</table>';

echo '
      </div>
    </div>
  </div>
</div>';

echo '
<div class="row" style="margin-top: 10px;">
  <div class="col-md-12">';

  print_alert_row(array('alert_test_id' => $vars['alert_test_id']));

echo '

  </div>
</div>';

?>

<div id="conditions_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <form id="edit" name="edit" method="post" class="form" action="">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><i class="oicon-traffic-light"></i> Edit Check Conditions</h3>
  </div>
  <div class="modal-body">

  <input type=hidden name="editing" value="check_conditions">
  <span class="help-block">Please exercise care when editing here.</span>
  <fieldset>
    <div class="control-group">
      <div class="controls">
        <textarea class="col-md-12" rows="4" name="check_conditions"><?php echo(htmlentities(implode("\n", $condition_text))); ?></textarea>
      </div>
    </div>
  </fieldset>

  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="icon-ok icon-white"></i> Save Changes</button>
  </div>
 </form>
</div>

<div id="delete_alert_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="delete_alert" aria-hidden="true">
 <form id="edit" name="edit" method="post" class="form" action="<?php echo(generate_url(array('page' => 'alert_checks'))); ?>">
  <input type="hidden" name="alert_test_id" value="<?php echo($check['alert_test_id']); ?>">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><i class="oicon-minus-circle"></i> Delete Alert Checker <?php echo($check['alert_test_id']); ?></h3>
  </div>
  <div class="modal-body">

  <span class="help-block">This will completely delete the alert checker and all device/entity associations.</span>
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="confirm">
        <strong>Confirm</strong>
      </label>
      <div class="controls">
        <label class="checkbox">
          <input type="checkbox" name="confirm" value="confirm" onchange="javascript: showWarning(this.checked);" />
          Yes, please delete this alert checker!
        </label>

 <script type="text/javascript">
        function showWarning(checked) {
          $('#warning').toggle();
          if (checked) {
            $('#delete_button').removeAttr('disabled');
          } else {
            $('#delete_button').attr('disabled', 'disabled');
          }
        }
      </script>

</div>
    </div>
  </fieldset>

	<div class="alert alert-message alert-danger" id="warning" style="display:none;">
    <h4 class="alert-heading"><i class="icon-warning-sign"></i> Warning!</h4>
    Are you sure you want to delete his alert checker?
  </div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button id="delete_button" type="submit" class="btn btn-danger" name="submit" value="delete_alert_checker" disabled><i class="icon-trash icon-white"></i> Delete Alert</button>
  </div>
 </form>
</div>
