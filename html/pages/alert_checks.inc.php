<?php

// Page to display list of configured alert checks

$alert_check = cache_alert_rules();
#$alert_assoc = cache_alert_assoc();

foreach (dbFetchRows("SELECT * FROM `alert_assoc` WHERE 1") as $entry)
{
  $alert_assoc[$entry['alert_test_id']][$entry['alert_assoc_id']]['entity_type'] = $entry['entity_type'];
  $alert_assoc[$entry['alert_test_id']][$entry['alert_assoc_id']]['attributes'] = unserialize($entry['attributes']);
  $alert_assoc[$entry['alert_test_id']][$entry['alert_assoc_id']]['device_attributes'] = unserialize($entry['device_attributes']);
}

foreach (dbFetchRows("SELECT * FROM `alert_table` LEFT JOIN `alert_table-state` ON `alert_table`.`alert_table_id` = `alert_table-state`.`alert_table_id`") as $entry)
{
  $alert_table[$entry['alert_test_id']][$entry['alert_table_id']] = $entry;
}

  echo '<table class="table table-condensed table-bordered table-striped table-rounded">
    <thead>
      <tr>
        <th></th><th></th>
        <th style="width: 25px">Id</th>
        <th style="width: 110px">Name</th>
        <th style="width: 300px">Tests</th>
        <th>Match</th>
        <th style="width: 40px">Entities</th>
      </tr>
    </thead>
    <tbody>', PHP_EOL;

foreach($alert_check as $check)
{

    // Generate list and popup for total number of entities which match this alert
    $entities = $alert_table[$check['alert_test_id']];
    #$entities_content = "";
    $s = array('up' => 0, 'down' => 0, 'unknown' => 0);
    #if(count($entities) < "15") { $e_sep = "<br />"; } else { $e_sep = ", "; }
    foreach($entities as $alert_table_id => $alert_table_entry)
    {
      #$entities_content[] = generate_entity_link($alert_table_entry['entity_type'], $alert_table_entry['entity_id'], $text = NULL, $graph_type = NULL);
      #print_vars($alert_table_entry);

      if($alert_table_entry['alert_status'] == '1') { $s['up']++;
      } elseif($alert_table_entry['alert_status'] == '0') { $s['down']++;
      } elseif($alert_table_entry['alert_status'] == '2') { $s['delay']++;
      } else { $s['unknown']++; }

    }
    #$entities_content = implode($e_sep, $entities_content);
    // End loop of entities

    if($s['up'] == count($entities))
    {
      $check['class']  = "green"; $check['table_tab_colour'] = "#194b7f"; $check['html_row_class'] = "";
    } elseif($s['down'] > '0') {
      $check['class']  = "red"; $check['table_tab_colour'] = "#cc0000"; $check['html_row_class'] = "error";
    } elseif($s['delay'] > '0') {
      $check['class']  = "orange"; $check['table_tab_colour'] = "#ff6600"; $check['html_row_class'] = "warning";
    } elseif($s['up'] > '0') {
      $check['class']  = "green"; $check['table_tab_colour'] = "#194b7f"; $check['html_row_class'] = "";
    } else {
      $check['class']  = "gray"; $check['table_tab_colour'] = "#555555"; $check['html_row_class'] = "disabled";
    }

    echo('<tr class="'.$check['html_row_class'].'">');

    echo('
      <td style="width: 1px; background-color: '.$check['table_tab_colour'].'; margin: 0px; padding: 0px"></td>
      <td style="width: 1px;"></td>');


    // Print the conditions applied by this alert

    echo '<td><strong>';
    echo $check['alert_test_id'];
    echo '</td>';

    echo '<td><strong>';
    echo $check['alert_name'];
    echo '</td>';

    // Loop the tests used by this alert
    echo '<td><strong>';
    foreach($check['conditions'] as $condition)
    {
      echo($condition['metric'].' ');
      echo($condition['condition'].' ');
      echo($condition['value']);
      echo('<br />');
    }
    echo('</strong></td>');

    echo('<td>');
    echo('<table class="table table-condensed-more table-bordered table-striped table-rounded">');
    echo '<thead>';
    echo('<tr>');
    echo('<th style="width: 50%;">Device Match</th>');
    echo('<th style="width: 50%;">Entity Match</th>');
    echo('</tr>');
    echo '</thead>';

    // Loop the associations which link this alert to this device
    foreach($alert_assoc[$check['alert_test_id']] as $assoc_id => $assoc)
    {

      echo('<tr>');
      echo('<td>');
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

      echo('</td>');

      echo('<td>');
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
      echo('</td>');

      echo('</tr>');

    }
    // End loop of associations

    echo '</table>';

    echo '</td>';

    // Print the count of entities this alert applies to and a popup containing a list and Print breakdown of entities by status.
    // We assume each row here is going to be two lines, so we just <br /> them.
    echo '<td>';
    #echo overlib_link('#', count($entities), $entities_content,  NULL));
    echo '<b>', count($entities), '</b>';
    echo '<br />';
    echo '<span class="green">', $s['up'], '</span>/<span class=red>', $s['down'], '</span>/<span class=gray>', $s['unknown'], '</span>';
    echo '</td>';


}

echo '</table>';


?>
