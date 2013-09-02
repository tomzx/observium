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

if($vars['editing'])
{
  // We are editing. Lets see what we are editing.

  if($vars['editing'] == "check_conditions")
  {
    $conds = array();
    foreach(explode('\n', $vars['check_conditions']) AS $cond)
    {
      list($this['metric'], $this['condition'], $this['value']) = explode(" ", $cond);
      $conds[] = $this;
    }
    $conds = json_encode($conds);
    $rows_updated = dbUpdate(array('conditions' => $conds), 'alert_tests', '`alert_test_id` = ?',array($vars['alert_test_id']));
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

echo '
<div class="row">
  <div class="col-md-12">
    <div class="well info_box">
      <div class="title"><i class="oicon-bell"></i> Checker Details</div>';

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
      <div class="content">';



   echo('<table class="table table-condensed table-bordered table-striped table-rounded">');
   echo('<thead><tr>');
   echo('<th style="width: 33%;">Metric</th>');
   echo('<th style="width: 33%;">Condition</th>');
   if ($_SESSION['userlevel'] >= '10') {
     echo '<th style="width: 33%;">Value <a href="#conditions_modal" class="pull-right" data-toggle="modal"> Edit</a></th>';
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

    echo('<table class="table table-condensed table-bordered table-striped table-rounded">');
    echo('<thead><tr>');
    echo('<th style="width: 45%;">Device Match</th>');
    echo('<th style="width: 45%;">Entity Match</th>');
    echo('</tr></thead>');


    foreach($assocs as $assoc_id => $assoc)
    {
      echo('<tr>');
      echo('<td>');
      echo('<strong>');
      $assoc['device_attributes'] = json_decode($assoc['device_attributes'], TRUE);
      if(is_array($assoc['device_attributes']))
      {
        foreach($assoc['device_attributes'] as $attribute)
        {
          echo($attribute['attrib'].' ');
          echo($attribute['condition'].' ');
          echo($attribute['value']);
          echo('<br />');
        }
      } else {
        echo("*");
      }
      echo("</strong><i>");
      echo('</td>');
      echo('<td><strong>');
      $assoc['attributes'] = json_decode($assoc['attributes'], TRUE);
      if(is_array($assoc['attributes']))
      {
        foreach($assoc['attributes'] as $attribute)
        {
          echo($attribute['attrib'].' ');
          echo($attribute['condition'].' ');
          echo($attribute['value']);
          echo('<br />');
        }
      } else {
        echo("*");
      }
    }

    echo('</table>');


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
    <h3 id="myModalLabel">Edit Check Conditions</h3>
  </div>
  <div class="modal-body">

  <input type=hidden name="editing" value="check_conditions">

  <fieldset>
    <div class="control-group">
      <div class="controls">
        <input type="textarea" class="col-md-12" rows="3" name="check_conditions" value="<?php echo(htmlentities(implode("\n", $condition_text)));  ?>"/>
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
