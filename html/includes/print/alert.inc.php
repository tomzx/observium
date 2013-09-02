<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

/**
 * Build alert table query from $vars
 * Returns queries for data, an array of parameters and a query to get a count for use in paging
 *
 * @param array $vars
 * @return array ($query, $param, $query_count)
 *
 */

function build_alert_table_query($vars)
{

  $args = array();
  $where = ' WHERE 1 ';

  // Loop through the vars building a sql query from relevant values
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        // Search by device_id if we have a device or device_id
        case 'device':
        case 'device_id':
          $where .= ' AND `device_id` = ?';
          $param[] = $value;
          break;
        case 'entity_type':
          if($value != 'all')
          {
            $where .= ' AND `entity_type` = ?';
            $param[] = $value;
          }
          break;
        case 'entity':
        case 'entity_id':
          $where .= ' AND `entity_id` = ?';
          $param[] = $value;
          break;
        case 'alert_test_id':
          $where .= ' AND `alert_test_id` = ?';
          $param[] = $value;
          break;
        case 'alerted':
          if($vars['alerted'] == '1')
          {
            $where .= " AND `alert_status` = ?";
            $param[] = '0';
          }
          break;
      }
    }
  }

 // If the user level is above global view, we don't need to constrain by permissions
 // If the user is below, join the devices_perms table and left join with device_id to only return permitted hosts.
 if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Build the query to get a count of entries
  $query_count = 'SELECT COUNT(alert_table_id) FROM `alert_table`';
  $query_count .= $query_perms;
  $query_count .= $where . $query_device . $query_user;

  // Query alerts count
#  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  // Build the query to get the list of entries
  $query = 'SELECT * FROM `alert_table` ';
  $query .= 'LEFT JOIN  `alert_table-state` ON  `alert_table`.`alert_table_id` =  `alert_table-state`.`alert_table_id`';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query .= ' ORDER BY `device_id`, `alert_test_id`, `entity_type`, `entity_id` DESC ';

#  if(isset($pagination) && $pagination)
#  {
#    $query .= 'LIMIT '.$start.','.$vars['pagesize'];
#  }

  return array($query, $param, $query_count);

}

/**
 * Display alert_table entries.
 *
 * @param array $vars
 * @return none
 *
 */
function print_alert_row($vars)
{

  global $alert_rules;

  // This should be set outside, but do it here if it isn't
  if(!is_array($alert_rules)) { $alert_rules = cache_alert_rules(); }
  /// WARN HERE

  // Short? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']);

  // With pagination? (display page numbers in header)
  if(isset($vars['pagination']) && $vars['pagination'])
  {
    $pagination = TRUE;
    $vars['pageno']     = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
    $vars['pagesize']   = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
    $start      = $vars['pagesize'] * $vars['pageno'] - $vars['pagesize'];
  }

  list($query, $param, $query_count) = build_alert_table_query($vars);

  // Fetch alerts
  $alerts = dbFetchRows($query, $param);

  // Set which columns we're going to show.
  // We hide the columns that have been given as search options via $vars
  $list = array('device' => FALSE, 'entity_id' => FALSE, 'entity_type' => FALSE, 'alert_test_id' => FALSE);
  foreach($list as $argument => $nope)
  {
    if (!isset($vars[$argument]) || empty($vars[$argument])) { $list[$argument] = TRUE; }
  }

  // Hide device if we know entity_id
  if(isset($vars['entity_id'])) { $list['device'] = FALSE; }
  // Hide entity_type if we know the alert_test_id
  if(isset($vars['alert_test_id'])) { $list['entity_type'] = FALSE; }


#  if ($pagination && !$short) { echo pagination($vars, $count); }

echo('<table class="table table-condensed table-bordered table-striped table-rounded table-hover">
  <thead>
    <tr>
      <th style="width: 1px;"></th>
      <th style="width: 1px;"></th>');
      // No table id
      //<th style="width: 5%;">Id</th>');

if($list['device']) {         echo('      <th style="width: 15%">Device</th>'); }
if($list['alert_test_id']) {  echo('      <th style="width: 15%">Alert</th>'); }
if($list['entity_type']) {    echo('      <th style="width: 10%">Type</th>'); }
if($list['entity_id']) {      echo('      <th style="">Entity</th>'); }

echo '
      <th style="width: 5%">State</th>
      <th>Message</th>
      <th style="width: 7.5%;">Checked</th>
      <th style="width: 7.5%;">Changed</th>
      <th style="width: 7.5%;">Alerted</th>
    </tr>
  </thead>
  <tbody>'.PHP_EOL;


  foreach($alerts AS $alert)
  {

    // Process the alert entry, generating colours and classes from the data
    humanize_alert_entry($alert);

    // Get the entity array using the cache
    $entity = get_entity_by_id_cache($alert['entity_type'], $alert['entity_id']);

    // Get the device array using the cache
    $device = device_by_id_cache($alert['device_id']);

    // Get the entity_descr.
    ### FIXME - This is probably duplicated effort from above. We should pass it $entity
    $entity_descr = entity_descr($alert['entity_type'], $alert['entity_id']);

    // Set the alert_rule from the prebuilt cache array
    $alert_rule = $alert_rules[$alert['alert_test_id']];

    echo('<tr class="'.$alert['html_row_class'].'">');
    echo('<td style="width: 1px; background-color: '.$alert['table_tab_colour'].'; margin: 0px; padding: 0px"></td>');
    echo('<td style="width: 1px;"></td>');
    // This would display the table id as hex, but even then it gets a bit long.
    #echo('<td>'.dechex($alert['alert_table_id']).'</td>');

    // If we know the device, don't show the device
    if ($list['device']) {
      echo('<td><span class="entity-title">'.generate_device_link($device).'</span></td>');
    }

    // If we're showing all entity types, print the entity type here
    if ($list['entity_type']) { echo('<td>'.nicecase($alert['entity_type']).'</td>'); }

    // Print link to the alert rule page
    if ($list['alert_test_id']) {
      echo '<td><a href="', generate_url(array('page' => 'alert_check', 'alert_test_id' => $alert_rule['alert_test_id'])), '">', $alert_rule['alert_name'], '</a></td>';
    }

    if($list['entity_id']) {
      echo('<td><span class="entity-title">'.generate_entity_link($alert['entity_type'], $alert['entity_id'], truncate($entity_descr, 40)).'</span></td>');
    }

    echo('<td>');
    ## FIXME -- generate a nice popup with parsed information from the state array
    echo(overlib_link("", "view", "<pre>".print_r(json_decode($alert['state'], TRUE), TRUE)."</pre>", NULL));
    echo('</td>');

    echo('<td class="'.$alert['class'].'">'.$alert['last_message'].'</td>');

    echo('<td>'.overlib_link('', $alert['checked'], format_unixtime($alert['last_checked'], 'r'), NULL).'</td>');
    echo('<td>'.overlib_link('', $alert['changed'], format_unixtime($alert['last_changed'], 'r'), NULL).'</td>');
    echo('<td>'.overlib_link('', $alert['alerted'], format_unixtime($alert['last_alerted'], 'r'), NULL).'</td>');

    echo('</tr>');

  }

echo '  </tbody>'.PHP_EOL;
echo '</table>'.PHP_EOL;

}
