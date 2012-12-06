<?php

$glo_conditions = cache_conditions_global();

#echo("<pre>");
#print_r($glo_conditions);
#echo("</pre>");

echo("<table class=\"table table-condensed table-striped\">
  <thead>
    <tr>
      <th>Type</th>
      <th>Subtype</th>
      <th style=\"width: 150px;\">Metric</th>
      <th style=\"width: 100px;\">Operator</th>
      <th style=\"width: 100px;\">Value</th>
      <th style=\"width: 75px;\">Severity</th>
      <th style=\"width: 50px;\">On</th>
      <th style=\"width: 50px;\">Status</th>
      <th></th>
    </tr>
  </thead>
  <tbody>\n");

function get_entity_list($type, $subtype = "*", $device_id = "*", $entry)
{
  if ($type == "storage") { $table = $type; } else { $table = $type.'s'; }
  if ($type == "port")    { $deleted = "deleted"; } else { $deleted = $type.'_deleted'; }

  $query = 'SELECT '.$type.'_id AS id, '.$deleted.' FROM '.$table.' WHERE 1';
  $args  = array();

  if (is_numeric($device_id))
  {
    $query .= " AND device_id = ?";
    $args[] = $device_id;
  }

  if (is_numeric($entry['entity']))
  {
    $query .= " AND ".$type."_id = ?";
    $args[] = $entry['entity'];
  }

  $entities_db = dbFetchRows($query, $args);

  foreach ($entities_db as $entity_db)
  {
    // Is this entity marked as deleted?
    if ($entity_db['deleted'] != "1")
    {
      $entities[] = $entity_db['id'];
    }
  }
  return $entities;
}

foreach ($glo_conditions as $type => $subtypes)
{
  foreach ($subtypes as $subtype => $metrics)
  {
    if (empty($subtype)) { $subtype = "*"; }
    foreach ($metrics as $metric => $entries)
    {
      foreach ($entries as $entry_id => $entry)
      {
        if ($entry['enable'] == 1) { $enabled = '<img align=absmiddle src="images/16/tick.png" />'; } else { $enabled = '<img align=absmiddle src="images/16/stop.png" />'; }
        echo("<tr>\n");
        echo('<td><strong>'.$type.'</strong></td><td>'.$subtype.'</td><td>'.$metric.'</td><td>'.htmlentities($entry['operator']).'</td><td>'.$entry['value'].'</td><td>'.$entry['severity'].'</td><td>'.$enabled.'</td>');
        echo("<td></td><td></td>\n");
        echo("</tr>\n");

        // Get which entities match this checker
        $entities = get_entity_list($type, $subtype, $device['device_id'], $entry['entity']);

        if (!empty($entities))
        {
          echo("<tr><td></td><td colspan=\"9\"><strong>");
          foreach ($entities as $entity)
          {
           echo('<button class="btn btn-small-thin" type="button">'.generate_entity_link($type, $entity).'</button>');
          }
          echo("</strong></td></tr>\n");
        }
      }
    }
  }
}

echo("  </tbody>\n");
echo("</table>\n");

?>
