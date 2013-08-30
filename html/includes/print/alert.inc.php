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

function build_alert_table_query($vars)
{

  $args = array();
  $where = ' WHERE 1 ';

  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
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
          $where .= ' AND `entity` = ?';
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

 if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  $query_count = 'SELECT COUNT(alert_table_id) FROM `alert_table`';
  $query_count .= $query_perms;
  $query_count .= $where . $query_device . $query_user;

  $query_count_param = $param;

  // Query alerts count
#  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

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

  $list = array('device' => FALSE, 'entity_id' => FALSE, 'entity_type' => FALSE, 'alert_test_id' => FALSE);

  foreach($list as $argument => $nope)
  {
    if (!isset($vars[$argument]) || empty($vars[$argument])) { $list[$argument] = TRUE; }
  }

#  if ($pagination && !$short) { echo pagination($vars, $count); }

echo('<table class="table table-condensed table-bordered table-striped table-rounded table-hover">
  <thead>
    <tr>
      <th></th>
      <th></th>');

#      <th style="width: 5%;">Id</th>');

if($list['device']) {         echo('      <th style="width: 15%">Device</th>'); }
if($list['alert_test_id']) {  echo('      <th style="width: 15%">Alert</th>'); }
if($list['entity_type']) {    echo('      <th style="width: 10%">Type</th>'); }
if($list['entity_id']) {      echo('      <th style="">Entity</th>'); }

echo '
      <th style="width: 8%">State</th>
      <th style="width: 15%;">Message</th>
      <th style="width: 8%;">Checked</th>
      <th style="width: 8%;">Changed</th>
      <th style="width: 8%;">Alerted</th>
    </tr>
  </thead>
  <tbody>'.PHP_EOL;


  foreach($alerts AS $alert)
  {

    humanize_alert_entry($alert);

    $entity = get_entity_by_id_cache($alert['entity_type'], $alert['entity_id']);
    $device = device_by_id_cache($alert['device_id']);
    $entity_descr = entity_descr($alert['entity_type'], $alert['entity_id']);
    $alert_rule = $alert_rules[$alert['alert_test_id']];

    echo('<tr class="'.$alert['html_row_class'].'">');
    echo('<td style="width: 1px; background-color: '.$alert['table_tab_colour'].'; margin: 0px; padding: 0px"></td>');
    echo('<td style="width: 1px;"></td>');
#    echo('<td>'.dechex($alert['alert_table_id']).'</td>');
    echo('<td><span class="entity-title">'.generate_device_link($device).'</span></td>');

    // If we're showing all entity types, print the entity type here
    if ($list['entity_type']) { echo('<td>'.$alert['entity_type'].'</td>'); }

    // Print link to the alert rule page
    if ($list['alert_test_id']) {
      echo '<td><a href="', generate_url(array('page' => 'alert_check', 'alert_test_id' => $alert_rule['alert_test_id'])), '">', $alert_rule['alert_name'], '</a></td>';
    }

    if($list['entity_id']) {
      echo('<td><span class="entity-title">'.generate_entity_link($alert['entity_type'], $alert['entity_id'], truncate($entity_descr, 40)).'</span></td>');
    }
    echo('<td>');
    ## FIXME -- generate a nice popup with parsed information from the state array 
    echo(overlib_link("", "view state", "<pre>".print_r(unserialize($alert['state']), TRUE)."</pre>", NULL));
    echo('</td>');

    echo('<td class="'.$alert['class'].'">'.$alert['last_message'].'</td>');

    echo('<td>'.$alert['checked'].'</td>');
    echo('<td>'.$alert['changed'].'</td>');
    echo('<td>'.$alert['alerted'].'</td>');

    echo('</tr>');

  }

echo '  </tbody>'.PHP_EOL;
echo '</table>'.PHP_EOL;

}
