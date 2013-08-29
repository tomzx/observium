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

/// Put this bit in to a function.

$check = dbFetchRow("SELECT * FROM `alert_tests` WHERE `alert_test_id` = ?", array($vars['alert_test_id']));

list($query, $param, $query_count) = build_alert_table_query($vars);

$query = str_replace(" * ", " `alert_status` ", $query);
$entities = dbFetchRows($query, $param);

$s = array('up' => 0, 'down' => 0, 'unknown' => 0, 'delay' => 0);
foreach($entities as $alert_table_id => $alert_table_entry)
{
  if($alert_table_entry['alert_status'] == '1') { $s['up']++;
  } elseif($alert_table_entry['alert_status'] == '0') { $s['down']++;
  } elseif($alert_table_entry['alert_status'] == '2') { $s['delay']++;
  } elseif($alert_table_entry['alert_status'] == '3') { $s['suppress']++;
  } else { $s['unknown']++; }
}

$check['alert_status'] = '<span class="green">'. $s['up']. '</span>/<span class="purple">'. $s['suppress']. '</span>/<span class=red>'. $s['down']. '</span>/<span class=orange>'. $s['delay']. '</span>/<span class=gray>'. $s['unknown']. '</span>';

/// End bit to go in to function

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
            <td><i>', $check['alert_status'], '</i></td>
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
  <div class="col-md-6">
    <div class="well info_box">
      <div class="title"><i class="oicon-bell"></i> Check Conditions</div>
      <div class="content">';

$conditions = unserialize($check['conditions']);

print_vars($conditions);


echo '
      </div>
    </div>
  </div>';


echo '
  <div class="col-md-6">
    <div class="well info_box">
      <div class="title"><i class="oicon-bell"></i> Associations</div>
      <div class="content">';

print_vars($assocs);


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
