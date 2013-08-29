<?php

// Alert test display and editing page.

$check = dbFetchRow("SELECT * FROM `alert_tests` WHERE `alert_test_id` = ?", array($vars['alert_test_id']));

echo '
<div class="row">
  <div class="col-md-12">
    <div class="well info_box">
      <div class="title"><i class="oicon-bell"></i> Checker Details</div>
      <div class="content">

        <table class="table table-striped table-bordered table-condensed table-rounded">
         <thead>
          <tr>
            <th style="width: 5%;">Test ID</th>
            <th style="width: 15%;">Entity Type</th>
            <th style="width: 20%;">Name</th>
            <th style="width: 60%;">Message</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>', $check['alert_test_id'], '</strong></td>
            <td><strong>', nicecase($check['entity_type']), '</strong></td>
            <td><strong>', $check['alert_name'], '</strong></td>
            <td><i>', $check['alert_message'], '</i></td>
          </tr>
        </tbody></table>
      </div>
    </div>
  </div>
</div>

<div class="row" style="margin-top: 10px;">
  <div class="col-md-12">';

  print_alert_row(array('alert_test_id' => $vars['alert_test_id']));

echo '

  </div>
</div>';

?>
