<?php

$alert_rules = cache_alert_rules();
$alert_assoc = cache_alert_assoc();
$alert_table = cache_device_alert_table($device['device_id']);

// Build Navbar

$navbar['class'] = "navbar-narrow";
$navbar['brand'] = "Alert Types";

foreach ($alert_table as $type => $thing)
{

  if (!$vars['type']) { $vars['type'] = $type; }
  if ($vars['type'] == $type) { $navbar['options'][$type]['class'] = "active"; }

  $navbar['options'][$type]['url'] = generate_url(array('page' => 'device', 'device' => $device['device_id'], 
                                                  'tab' => 'alerts', 'type' => $type));
  $navbar['options'][$type]['text'] = htmlspecialchars(nicecase($type));
}

$navbar['options_right']['update']['url']  = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'alerts', 'action'=>'update'));
$navbar['options_right']['update']['text'] = 'Regenerate';
if ($vars['action'] == 'update') { $navbar['options_right']['update']['class'] = 'active'; }

print_navbar($navbar);

// Run actions

if($vars['action'] == 'update')
{
  update_device_alert_table($device);
  $alert_table = cache_device_alert_table($device['device_id']);
}

if(is_numeric($vars['alert']) && FALSE)
{

} else {

  // Generate listing page

  echo('<table class="table table-condensed table-bordered table-striped table-rounded">
    <thead>
      <tr>
        <th style="width: 35px">Id</th>
        <th style="width: 175px">Tests</th>
        <th style="width: 400px">Match</th>
        <th style="width: 35px">Entities</th>
        <th style="width: 35px;">Status</th>
        <th style="width: 35px;">Delay</th>
        <th style="width: 35px;">Severity</th>
        <th style="width: 35px;">Alerters</th>
        <th style="width: 35px;">On</th>
        <th style="width: 35px;">View</th>
      </tr>
    </thead>
    <tbody>'.PHP_EOL);

  // Loop the associations array

  #echo('<pre>');
  #print_vars($alert_table);
  #echo('</pre>');

  foreach ($alert_table[$vars['type']] as $entity_id => $alert_list)
  {
    // Check that the entity_type matches the one we're interested in.
    foreach($alert_list as $alert_test_id => $alert_args)
    {
#      $alerts[$alert_test_id]['data'] = $alert_args;
      foreach(explode(",", $alert_args['alert_assocs']) AS $assoc)
      {
        $alerts[$alert_test_id]['assoc'][$assoc]['ids'][$alert_args['alert_table_id']] = $entity_id;
        $alerts[$alert_test_id]['ids'][$alert_args['alert_table_id']] = $entity_id;
      }
    }
  }

#  echo('<pre>');
#  print_vars($alert_table);
#  echo('</pre>');


  foreach ($alerts as $alert_test_id => $alert_entry)
  {

  #echo('<pre>');
  #print_vars($alert_entry);
  #echo('</pre>');


    $alert = $alert_rules[$alert_test_id];
    list($entity_table, $entity_id_field, $entity_descr_field) = entity_type_translate ($alert['entity_type']);
    $link = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'alerts', 'type' => $alert['entity_type'], 'alert' => $alert_test_id));

    $entities = $alert_entry['ids'];

    $s = array('up' => 0, 'down' => 0, 'unknown' => 0);

    // Generate list and popup for total number of entities which match this alert
    $entities_content = "";
    if(count($entities) < "15") { $e_sep = "<br />"; } else { $e_sep = ", "; }
    foreach($entities as $alert_table_id => $entity_id)
    {
      $alert_table_entry = $alert_table[$vars['type']][$entity_id][$alert_test_id];
      $entities_content[] = generate_entity_link($vars['type'], $entity_id, $text = NULL, $graph_type=NULL);
      #print_vars($alert_table_entry);

      if($alert_table_entry['alert_status'] == '1') { $s['up']++;
      } elseif($alert_table_entry['alert_status'] == '0') { $s['down']++;
      } else { $s['unknown']++; }

    }
    $entities_content = implode($e_sep, $entities_content);

    echo('<tr>');

    // Print the conditions applied by this alert

    echo('<td><strong>');
    echo($alert_test_id);
    echo('</td>');

    // Loop the tests used by this alert
    echo('<td><strong>');
    foreach($alert['conditions'] as $condition)
    {
      echo($condition['metric'].' ');
      echo($condition['condition'].' ');
      echo($condition['value']);
      echo('<br />');
    }
    echo('</strong></td>');

    echo('<td>');
    echo('<table class="table table-condensed table-bordered table-striped table-rounded">');
    echo('<tr>');
    echo('<th style="width: 45%;">Device Match</th>');
    echo('<th style="width: 45%;">Entity Match</th>');
    echo('<th>#</th>');
    echo('</tr>');

    #if($debug)
    #{
    #echo('<pre> Alert Array');
    #print_vars($alert);
    #echo('</pre>');
    #}


    // Loop the associations which link this alert to this device
    foreach($alert_entry['assoc'] as $assoc_id => $assoc_data)
    {
      #print_vars($assoc_id);
      $assoc = $alert_assoc[$assoc_id];

      echo('<tr>');
      echo('<td>');
      echo('<strong>');
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
      echo('</strong></td>');

      // Print the count of entities this alert applies to and a popup containing a list

      $a_entities = $assoc_data['ids'];
      echo('<td>');
      $a_content = "";
      if(count($a_entities) < "15") { $e_sep = "<br />"; } else { $e_sep = ", "; }
      foreach($entities as $entity)
      {
        $a_content[] = generate_entity_link($vars['type'], $entity, $text = NULL, $graph_type=NULL);
      }
      $a_content = implode($e_sep, $a_content);

      echo(overlib_link('#', count($a_entities), $a_content,  NULL));
      echo('</td>');

      echo('</tr>');

    }
    // End loop of associations

    echo('</table>');
    echo('</td>');


    // Print the count of entities this alert applies to and a popup containing a list
    echo('<td>');
    echo(overlib_link('#', count($entities), $entities_content,  NULL));
    echo('</td>');

    echo('<td>');
    echo('<span class="green">'.$s['up'].'</span>/<span class=red>'.$s['down'].'</span>/<span class=gray>'.$s['unknown'].'</span>');
    echo('</td>');

    echo('<td>' . $alert['delay'] . '</td>');

    echo('<td>' . $alert['severity'] . '</td>');
    echo('<td>' . overlib_link('#', $alert['alerter'], generate_alerter_info($alert['alerter']),  NULL) . '</td>');
    echo('<td>' . $alert['enable'] . '</td>');
    echo('<td><a href="'.$link.'">View</a></td>');
    echo('</tr>');

    // Show detailed output for this alert:

    if(isset($vars['alert']) && $vars['alert'] == $alert_test_id)
    {
      echo('<tr><td colspan="11">');
      echo('<table  class="table table-condensed table-bordered table-striped table-rounded">');
      echo('<tr>');
      echo('<th></th><th></th>');
      echo('<th style="width: 60px;">Alert Id</th>');
      echo('<th style="width: 300px;">Entity</th>');
      echo('<th>State</th>');
      echo('<th style="width: 150px;">Message</th>');
      echo('<th style="width: 150px;">Checked</th>');
      echo('<th style="width: 150px;">Changed</th>');
      echo('<th style="width: 150px;">Alerted</th>');
      echo('<th style="width: 30px;">#</th>');

      echo('</tr>');
      foreach($entities as $alert_table_id => $entity_id)
      {
        $alert_table_entry = &$alert_table[$vars['type']][$entity_id][$alert_test_id];
        humanize_alert_entry($alert_table_entry);

        ## FIXME -- move this to humanize_alert_entry
        if($alert_table_entry['last_checked'] == '0') { $alert_table_entry['lc'] = "<i>Never</i>"; } else { $alert_table_entry['lc'] = formatUptime(time()-$alert_table_entry['last_checked']); }
        if($alert_table_entry['last_changed'] == '0') { $alert_table_entry['c']  = "<i>Never</i>"; } else { $alert_table_entry['c'] = formatUptime(time()-$alert_table_entry['last_changed']); }
        if($alert_table_entry['last_alerted'] == '0') { $alert_table_entry['la']  = "<i>Never</i>"; } else { $alert_table_entry['la'] = formatUptime(time()-$alert_table_entry['last_alerted']); }

        echo('<tr class="'.$alert_table_entry['html_row_class'].'">');

        echo('
         <td style="width: 1px; background-color: '.$alert_table_entry['table_tab_colour'].'; margin: 0px; padding: 0px"></td>
         <td style="width: 1px;"></td>');

        echo('<td>'.$alert_table_id.'</td>');
        echo('<td>'.generate_entity_link($vars['type'], $entity_id, $text = NULL, $graph_type=NULL).'</td>');
        echo('<td>');
        ## FIXME -- generate a nice popup with parsed information from the state array
        echo(overlib_link("", "view state", "<pre>".print_r(unserialize($alert_table_entry['state']), TRUE)."</pre>", NULL));
        echo('</td>');
        echo('<td class="'.$alert_table_entry['class'].'">'.$alert_table_entry['last_message'].'</td>');
        echo('<td>'.$alert_table_entry['lc'].'</td>');
        echo('<td>'.$alert_table_entry['c'].'</td>');
        echo('<td>'.$alert_table_entry['la'].'</td>');
#        echo('<td class="'.$alert_table_entry['class'].'">'.$alert_table_entry['alert_status'].'</td>');
        echo('<td>'.$alert_table_entry['count'].'</td>');
        echo('</tr>');
      }
      echo('</table>');
      echo('</td></tr>');
    }
  }

  echo("  </tbody>\n");
  echo("</table>\n");

}

?>
