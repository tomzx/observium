<?php

$glo_conditions = cache_conditions_global();

$navbar['class'] = "navbar-narrow";
$navbar['brand'] = "Alert Types";

foreach ($glo_conditions as $type => $sub_types)
{
  if (!$vars['type']) { $vars['type'] = $type; }
  if ($vars['type'] == $type) { $navbar['options'][$type]['class'] = "active"; }

  $navbar['options'][$type]['url'] = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'alerts', 'type' => $type));
  $navbar['options'][$type]['text'] = htmlspecialchars(nicecase($type));
}

unset($type);
unset($sub_types);

print_navbar($navbar);

echo('<table class="table table-condensed table-bordered table-striped table-rounded">
  <thead>
    <tr>
      <th style="min-width: 100px;">Entity Class</th>
      <th>Description Regex</th>
      <th style="width: 150px;">Metric</th>
      <th style="width: 100px;">Operator</th>
      <th style="width: 100px;">Value</th>
      <th style="width: 45px;">Count</th>
      <th style="width: 65px;">Severity</th>
      <th style="width: 50px;">On</th>
    </tr>
  </thead>
  <tbody>'.PHP_EOL);

// FIXME - Converty this to take an array. Much easier.

function get_entity_list($type, $subtype = "*", $device_id = "*", $entry, $descr_regex = NULL)
{
  if ($type == "storage") { $table = $type; } else { $table = $type.'s'; }
  if ($type == "port")    { $deleted = "deleted"; } else { $deleted_d = $type.'_deleted'; }
  if ($type == "sensor")  { $descr_field = "sensor_descr"; }

  $query = 'SELECT *,'.$type.'_id AS id';
  if($deleted) { $query .= ', '.$deleted; }
  $query .= ' FROM '.$table.' WHERE 1';
  $args  = array();

  if (is_numeric($device_id))
  {
    $query .= " AND device_id = ?";
    $args[] = $device_id;
  }

  if ($subtype != "*" && strlen($subtype))
  {
    switch($type)
    {
      case "sensor":
        $query .= " AND sensor_class = ?";
        $args[] = $subtype;
        break;
    }
  }

  if (is_numeric($entry['entity']))
  {
    $query .= " AND ".$type."_id = ?";
    $args[] = $entry['entity'];
  }

#  echo("$query");

  $entities_db = dbFetchRows($query, $args);

  foreach ($entities_db as $entity_db)
  {

    if($descr_regex)
    {
      if(preg_match($descr_regex, $entity_db[$descr_field])) { $entity_db['ignore_this'] = FALSE; } else { $entity_db['ignore_this'] = TRUE; }
    }

    // Is this entity marked as deleted?
    if ($entity_db['deleted'] != "1" && !$entity_db['ignore_this'])
    {
      $entities[] = $entity_db['id'];
    }
  }
  return $entities;
}

#foreach ($glo_conditions as $type => $subtypes)
#{



  foreach ($glo_conditions[$vars['type']] as $subtype => $metrics)
  {
    if (empty($subtype)) { $subtype = "*"; }
    foreach ($metrics as $metric => $entries)
    {
      foreach ($entries as $entry_id => $entry)
      {
        if ($entry['enable'] == 1) { $enabled = '<img align=absmiddle src="images/16/tick.png" />'; } else { $enabled = '<img align=absmiddle src="images/16/stop.png" />'; }
        if($entry['severity'] == "crit") { $severity_class = "text-error"; } elseif($entry['severity'] == "warn") { $severity_class = "text-warning"; } else { $severity_class = NULL; }


        echo("<tr>\n");
        echo('<td>'.$subtype.'</td><td>'.htmlentities($entry['descr_regex']).'</td><td>'.$metric.'</td><td>'.htmlentities($entry['operator']).'</td><td>'.$entry['value'].'</td><td>'.$entry['count'].'</td><td><strong class="'.$severity_class.'">'.$entry['severity'].'</strong></td><td>'.$enabled.'</td>');
        echo("</tr>\n");

        // Get which entities match this checker
        $entities = get_entity_list($vars['type'], $subtype, $device['device_id'], $entry['entity'], $entry['descr_regex']);

        if (!empty($entities))
        {
          echo("<tr><td></td><td colspan=\"9\"><strong>");
          foreach ($entities as $entity)
          {
           echo('<button class="btn btn-small-thin" type="button" style="margin: 2px;">'.generate_entity_link($vars['type'], $entity).'</button>');
          }
          echo("</strong></td></tr>\n");
        }
      }
    }
  }
#}

echo("  </tbody>\n");
echo("</table>\n");

?>
