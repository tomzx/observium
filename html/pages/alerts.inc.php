<?php

// Run actions

if($vars['action'] == 'update')
{
  foreach(dbFetchRows("SELECT * FROM `devices`") AS $device)
  {
    update_device_alert_table($device);
  }

  unset($vars['action']);

}

$navbar['class'] = "navbar-narrow";
$navbar['brand'] = "Alert Types";

$types = dbFetchRows("SELECT `entity_type` FROM `alert_tests` GROUP BY `entity_type`");

$navbar['options']['all']['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => 'all'));
$navbar['options']['all']['text'] = htmlspecialchars(nicecase('all'));
if ($vars['entity_type'] == 'all') {
  $navbar['options']['all']['class'] = "active";
  $navbar['options']['all']['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => NULL));
}

foreach ($types as $thing)
{
  if (!$vars['entity_type']) { $vars['entity_type'] = $thing['entity_type']; }
  if ($vars['entity_type'] == $thing['entity_type'])
  {
    $navbar['options'][$thing['entity_type']]['class'] = "active";
    $navbar['options'][$thing['entity_type']]['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => NULL));
  } else {
    $navbar['options'][$thing['entity_type']]['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => $thing['entity_type']));
  }
  $navbar['options'][$thing['entity_type']]['text'] = htmlspecialchars(nicecase($thing['entity_type']));
}

$navbar['options_right']['alarmed']['url']  = generate_url($vars, array('page' => 'alerts', 'alerted' => '1'));
$navbar['options_right']['alarmed']['text'] = 'Alarmed Only';
if ($vars['alerted'] == '1') { $navbar['options_right']['alarmed']['class'] = 'active';
$navbar['options_right']['alarmed']['url']  = generate_url($vars, array('page' => 'alerts', 'alerted' => NULL));}


$navbar['options_right']['update']['url']  = generate_url($vars, array('page' => 'alerts', 'action'=>'update'));
$navbar['options_right']['update']['text'] = 'Regenerate';
if ($vars['action'] == 'update') { $navbar['options_right']['update']['class'] = 'active'; }

print_navbar($navbar);

$alert_rules = cache_alert_rules();

echo('<table class="table table-condensed table-bordered table-striped table-rounded">
  <thead>
    <tr>
      <th></th>
      <th></th>
      <th style="width: 150px">Device</th>
');

if ($vars['entity_type'] == 'all') {
  echo('
      <th style="width: 50px;">Type</th>
');
}

echo('
      <th style="width: 100px;">Alert</th>
      <th style="">Entity</th>
      <th style="width: 90px;">Checked</th>
      <th style="width: 90px;">Changed</th>
      <th style="width: 90px;">Alerted</th>
    </tr>
  </thead>
  <tbody>'.PHP_EOL);

  $args = array();
  $sql  = "SELECT * FROM  `alert_table`";
  $sql .= " LEFT JOIN  `alert_table-state` ON  `alert_table`.`alert_table_id` =  `alert_table-state`.`alert_table_id` WHERE 1";
  if($vars['alerted'] == '1')
  {
    $sql .= " AND `alert_status` = '0'";
  }
  if(isset($vars['entity_type']) && $vars['entity_type'] != 'all')
  {
    $sql .= " AND `entity_type` = ?";
    $args[] = $vars['entity_type'];
  }
  $sql .= " ORDER BY `device_id`,`entity_type`";

  $alerts = dbFetchRows($sql, $args);

  foreach($alerts as $alert_entry)
  {

    humanize_alert_entry($alert_entry);

    $entity = get_entity_by_id_cache($alert_entry['entity_type'], $alert_entry['entity_id']);
    $device = device_by_id_cache($alert_entry['device_id']);
    $entity_descr = entity_descr($alert_entry['entity_type'], $alert_entry['entity_id']);
    $alert_rule = &$alert_rules[$alert_entry['alert_test_id']];

    echo('<tr class="'.$alert_entry['html_row_class'].'">');
    echo('<td style="width: 1px; background-color: '.$alert_entry['table_tab_colour'].'; margin: 0px; padding: 0px"></td>');
    echo('<td style="width: 1px;"></td>');
    echo('<td><span class="entity-title">'.generate_device_link($device).'</span></td>');

if ($vars['entity_type'] == 'all') {
    echo('<td>'.$alert_entry['entity_type'].'</td>');
}

    echo('<td>'.$alert_rule['alert_name'].'</td>');
    echo('<td><span class="entity-title">'.generate_entity_link($alert_entry['entity_type'], $alert_entry['entity_id'], truncate($entity_descr, 40)).'</span></td>');

    echo('<td>'.$alert_entry['checked'].'</td>');
    echo('<td>'.$alert_entry['changed'].'</td>');
    echo('<td>'.$alert_entry['alerted'].'</td>');

    echo('</tr>');
  }

echo("  </tbody>\n");
echo("</table>\n");

?>
