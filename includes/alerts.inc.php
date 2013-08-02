<?php

/**
 * Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Adam Armstrong - http://www.observium.org
 *
 * @package    observium
 * @subpackage alerter
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */


/**
 * Check an entity against all relevant alerts
 *
 * @param string type
 * @param array entity
 * @param array data
 * @return NULL
 */

function check_entity($type, $entity, $data)
{
  global $config, $alert_rules, $alert_table;

  echo("\nChecking alerts\n");

  #print_r($data);

  list($entity_table, $entity_id_field, $entity_descr_field) = entity_type_translate ($type);

  foreach($alert_table[$type][$entity[$entity_id_field]] as $alert_test_id => $alert_args)
  {

      if($alert_rules[$alert_test_id]['and']) { $alert = TRUE; } else { $alert = FALSE; }

      $update_array = array();

      if(is_array($alert_rules[$alert_test_id]))
      {

        echo("Checking alert ".$alert_test_id." associated by ".$alert_args['alert_assocs']."\n");
        var_dump($alert);

        foreach($alert_rules[$alert_test_id]['conditions'] as $test_key => $test)
        {

          if (substr($test['value'],0,1)=="@")
          {
            $ent_val = substr($test['value'],1); $test['value'] = $entity[$ent_val];
            echo(" replaced @".$ent_val." with ". $test['value'] ." from entity. ");
          }

          echo("Testing: " . $test['metric']. " ". $test['condition'] . " " .$test['value']);
          $update_array['state']['metrics'][$test['metric']] = $data[$test['metric']];

          if(isset($data[$test['metric']]))
          {
            echo(" (value: ".$data[$test['metric']].")");
            if(test_condition($data[$test['metric']], $test['condition'], $test['value']))
            {
              // A test has failed. Set the alert variable and make a note of what failed.
              echo(" FAIL ");
              $update_array['state']['failed'][] = $test;

              if($alert_rules[$alert_test_id]['and']) { $alert  = ($alert && TRUE);
                                               } else { $alert = ($alert || TRUE); }

            } else {
              if($alert_rules[$alert_test_id]['and']) { $alert = ($alert && FALSE);
                                               } else { $alert = ($alert || FALSE); }
              echo(" OK ");
            }
          } else {
            echo("  Metric is not present on entity.\n");
            if($alert_rules[$alert_test_id]['and']) { $alert  = ($alert && FALSE);
                                             } else { $alert =  ($alert || FALSE); }
          }
        }

        if($alert)
        {
          $update_array['count'] = $alert_args['count']+1;

          // Check against the alert test's delay
          if($update_array['count'] >= $alert_rules[$alert_test_id]['delay'])
          {
            // This is a real alert.
            echo(" Checks failed. Generate alert.\n");
            $update_array['alert_status'] = '0';
            $update_array['last_message'] = 'Checks failed';
            $update_array['last_checked'] = time();
            if($alert_args['alert_status'] != '0'  || $alert_args['last_changed'] == '0') { $update_array['last_changed'] = time(); $update_array['last_alerted'] = '0'; }
          } else {
            // This is alert needs to exist for longer.
            echo(" Checks failed. Delaying alert.\n");
            $update_array['alert_status'] = '2';
            $update_array['last_message'] = 'Checks failed (delayed)';
            $update_array['last_checked'] = time();
            if($alert_args['alert_status'] != '2'  || $alert_args['last_changed'] == '0') { $update_array['last_changed'] = time(); $update_array['last_alerted'] = '0'; }
          }
        } else {
          $update_array['count'] = 0;
          // Alert conditions passed. Record that we tested it and update status and other data.
          echo(" Checks OK.\n");
          $update_array['alert_status'] = '1';
          $update_array['last_message'] = 'Checks OK';
          $update_array['last_checked'] = time();
          #$update_array['count'] = 0;
          if($alert_args['alert_status'] != '1' || $alert_args['last_changed'] == '0') { $update_array['last_changed'] = time(); }
        }

        // Serialize the state array before we put it into MySQL.
        $update_array['state'] = serialize($update_array['state']);
#        $update_array['alert_table_id'] = $alert_args['alert_table_id'];

        /// Perhaps this is better done with SQL replace?
        #print_r($alert_args);
        if(!$alert_args['state_entry'])
        {
          // State entry seems to be missing. Insert it before we update it.
          dbInsert(array('alert_table_id' => $alert_args['alert_table_id']), 'alert_table-state');
          echo("INSERTING");
        }

        dbUpdate($update_array, 'alert_table-state', '`alert_table_id` = ?', array($alert_args['alert_table_id']));


      } else {
        echo("Alert missing!");
      }
  }
}


/**
 * Build an array of conditions that apply to a supplied device
 *
 * This takes the array of global conditions and removes associations that don't match the supplied device array
 *
 * @return array
*/

function cache_device_conditions($device)
{
  $conditions = cache_conditions();

  foreach($conditions['assoc'] as $assoc_key => $assoc)
  {
    if(match_device($device, $assoc['device_attributes']))
    {
      echo(" Matched $assoc_key");
    } else {
      echo(" Did not match $assoc_key");
      unset($conditions['assoc'][$assoc_key]);
    }
  }
  return $conditions;
}



/**
 * Fetch array of alerts to a supplied device from `alert_table`
 *
 * This takes device_id as argument and returns an array.
 *
 * @param device_id
 * @return array
*/

function cache_device_alert_table($device_id)
{

  $alert_table = array();

  $sql  = "SELECT *,`alert_table`.`alert_table_id` AS `alert_table_id` FROM  `alert_table`";
  $sql .= " LEFT JOIN  `alert_table-state` ON  `alert_table`.`alert_table_id` =  `alert_table-state`.`alert_table_id`";
  $sql .= " WHERE  `device_id` =  ?";

  foreach (dbFetchRows($sql, array($device_id)) as $entry)
  {
    $alert_table[$entry['entity_type']][$entry['entity_id']][$entry['alert_test_id']] = $entry;
  }

  return $alert_table;

}

/**
 * Build an array of all alert rules
 *
 * @return array
*/

function cache_alert_rules()
{

  $alert_rules = array();
  $rule_count = 0;

  foreach (dbFetchRows("SELECT * FROM `alert_tests`") as $entry)
  {
    if($entry['alerter'] == '') {$entry['alerter'] = "default"; }
    $alert_rules[$entry['alert_test_id']] = $entry;
    $alert_rules[$entry['alert_test_id']]['conditions'] = unserialize($entry['conditions']);
    $rules_count++;
  }

  if($_GLOBAL['debug']) { echo("Cached $rules_count alert rules.\n"); }

  return $alert_rules;
}

function generate_alerter_info($alerter)
{

  global $config;

  if(is_array($config['alerts']['alerter'][$alerter]))
  {
    $a = $config['alerts']['alerter'][$alerter];
    $output  = "<strong>".$a['descr']."</strong><hr />";
    $output .= $a['type'].": ".$a['contact']."<br />";
    if($a['enable']) { $output .= "Enabled"; } else { $output .= "Disabled"; }
    return $output;
  } else {
    return "Unknown alerter.";
  }

}

function cache_alert_assoc()
{

  $alert_assoc = array();
  $rule_count = 0;

  foreach (dbFetchRows("SELECT * FROM `alert_assoc`") as $entry)
  {
    $attributes = unserialize($entry['attributes']);
    $dev_attrib = unserialize($entry['device_attributes']);
    $alert_assoc[$entry['alert_assoc_id']]['entity_type'] = $entry['entity_type'];
    $alert_assoc[$entry['alert_assoc_id']]['attributes'] = $attributes;
    $alert_assoc[$entry['alert_assoc_id']]['device_attributes'] = $dev_attrib;
    $alert_assoc[$entry['alert_assoc_id']]['alert_test_id']      = $entry['alert_test_id'];
  }

  return $alert_assoc;
}

/**
 * Build an array of all conditions
 *
 * @return array
*/

function cache_conditions()
{

  $cache = array();

  foreach (dbFetchRows("SELECT * FROM `alert_tests`") as $entry)
  {
    $conditions = unserialize($entry['conditions']);
    $cache['cond'][$entry['alert_test_id']]['entity_type'] = $entry['entity_type'];
    $cache['cond'][$entry['alert_test_id']]['conditions'] = $conditions;
  }

  foreach (dbFetchRows("SELECT * FROM `alert_assoc`") as $entry)
  {
    $attributes = unserialize($entry['attributes']);
    $dev_attrib = unserialize($entry['device_attributes']);
    $cache['assoc'][$entry['alert_assoc_id']]['entity_type']       = $entry['entity_type'];
    $cache['assoc'][$entry['alert_assoc_id']]['attributes']        = $attributes;
    $cache['assoc'][$entry['alert_assoc_id']]['device_attributes'] = $dev_attrib;
    $cache['assoc'][$entry['alert_assoc_id']]['alert_test_id']     = $entry['alert_test_id'];
  }

  return $cache;
}

/**
 * Compare two values
 *
 * @param value_a
 * @param condition
 * @param value_b
 * @return integer
*/

function test_condition($value_a, $condition, $value_b)
{

    $value_a = trim($value_a);
    $value_b = trim($value_b);

    switch($condition)
    {
      case 'ge':
      case '>=':
       if ($value_a >= $value_b) { $alert = TRUE; } else { $alert = FALSE; }
       break;
      case 'le':
      case '<=':
       if ($value_a <= $value_b) { $alert = TRUE; } else { $alert = FALSE; }
       break;
      case 'greater':
      case '>':
       if ($value_a > $value_b) { $alert = TRUE; } else { $alert = FALSE; }
       break;
      case 'less':
      case '<':
       if ($value_a < $value_b) { $alert = TRUE; } else { $alert = FALSE; }
       break;
      case 'notequals':
      case '!=':
       if ($value_a != $value_b) { $alert = TRUE; } else { $alert = FALSE; }
       break;
      case 'equals':
      case '=':
       if ($value_a == $value_b) { $alert = TRUE; } else { $alert = FALSE; }
       break;
      case 'match':
        $value_b = str_replace('*', '.*', $value_b);
        $value_b = str_replace('?', '.', $value_b);
        if(preg_match('/^'.$valueb.'$/', $value)) { $alert = TRUE; } else { $alert = FALSE; }
        break;
      case 'notmatch':
        $value_b = str_replace('*', '.*', $value_b);
        $value_b = str_replace('?', '.', $value_b);
        if(preg_match('/^'.$valueb.'$/', $value)) { $alert = FALSE; } else { $alert = TRUE; }
        break;
      default:
        $alert = FALSE;
        break;
    }

    return $alert;
}


/**
 * Return an array of devices which match a set of attribute rules.
 *
 * @param array attributes
 * @return array
*/

/// FIXME - perhaps do this from device cache?

function match_devices($attributes)
{

  $sql  = "SELECT * FROM `devices`";
  $sql .= ' WHERE 1';

  foreach($attributes as $attrib)
  {
    switch ($attrib['condition'])
    {
      case 'equals':
        $sql .= " AND `".$attrib['attrib']."` = ?";
        $param[] = $attrib['value'];
        break;
      case 'match':
        $attrib['value'] = str_replace("*", "%", $attrib['value']);
        $sql .= " AND `".$attrib['attrib']."` LIKE ?";
        $param[] = $attrib['value'];
        break;
    }
  }

  $devices = dbFetchRows($sql, $param);

  return $devices;

}

/**
 * Test if a device matches a set of attributes
 * Uses a supplied device array for matching.
 *
 * @param array device
 * @param array attributes
 * @return boolean
*/

function match_device($device, $attributes)
{

  $failed  = 0;
  $success = 0;

  foreach($attributes as $attrib)
  {
    switch ($attrib['condition'])
    {
      case 'equals':
        if($device[$attrib['attrib']] == $attrib['value']) { $success++; } else { $fail++; }
        break;
      case 'match':
        $attrib['value'] = str_replace('*', '.*', $attrib['value']);
        $attrib['value'] = str_replace('?', '.', $attrib['value']);
        if(preg_match('/^'.$attrib['value'].'$/', $device[$attrib['attrib']])) { $success++; } else { $fail++; }
        break;
    }
  }

  if(strlen($attributes) == 0) { $success++; }

  if($fail || $success == 0) {
    return FALSE;
  } else {
    return TRUE;
  }

}

/**
 * Return an array of entities of a certain type which match device attribute and entity attribute rules.
 *
 * @param array dev_attributes
 * @param array attributes
 * @param string entity_type
 * @return array
*/

/// FIXME - this is going to be horribly slow.

function match_entities($dev_attributes, $attributes, $entity_type)
{

  list($entity_table, $entity_id_field, $entity_descr_field) = entity_type_translate ($entity_type);

  $sql   = "SELECT * FROM `devices` AS D, `".mres($entity_table)."` AS E";
  $sql  .= " WHERE E.device_id = D.device_id";

  foreach($dev_attributes as $attrib)
  {
    switch ($attrib['condition'])
    {
      case 'equals':
        $sql .= " AND D.`".mres($attrib['attrib'])."` = ?";
        $param[] = $attrib['value'];
        break;
      case 'match':
        $attrib['value'] = str_replace("*", "%", $attrib['value']);
        $sql .= " AND `".mres($attrib['attrib'])."` LIKE ?";
        $param[] = $attrib['value'];
        break;
    }
  }

  foreach($attributes as $attrib)
  {
    switch ($attrib['condition'])
    {
      case 'equals':
        $sql .= " AND E.`".$attrib['attrib']."` = ?";
        $param[] = $attrib['value'];
        break;
      case "greater":
      case ">":
        $sql .= " AND `".$attrib['attrib']."` > ?";
        $param[] = $attrib['value'];
        break;
      case 'match':
        $attrib['value'] = str_replace("*", "%", $attrib['value']);
        $sql .= " AND `".$attrib['attrib']."` LIKE ?";
        $param[] = $attrib['value'];
        break;
    }
  }

  $entities = dbFetchRows($sql, $param);
  return $entities;

}

/**
 * Translate an entity type to the relevant table and the identifier field name
 *
 * @param string entity_type
 * @return string entity_table
 * @return string entity_id
*/

function entity_type_translate ($entity_type)
{

  switch($entity_type)
  {
    case "mempool":
      $entity_id_field    = "mempool_id";
      $entity_descr_field = "mempool_descr";
      $entity_table       = "mempools";
      break;
    case "processor":
      $entity_id_field    = "processor_id";
      $entity_descr_field = "processor_descr";
      $entity_table       = "processors";
      break;
    case "port":
      $entity_id_field    = "port_id";
      $entity_descr_field = "ifDescr";
      $entity_table       = "ports";
      break;
    case "sensor":
      $entity_id_field    = "sensor_id";
      $entity_descr_field = "sensor_descr";
      $entity_table       = "sensors";
      break;
    case "bgp_peer":
      $entity_id_field    = "bgpPeer_id";
      $entity_descr_field = "bgpPeerRemoteAddr";
      $entity_table       = "bgpPeers";
      break;
    case "netscaler_vsvr":
      $entity_id_field    = "vsvr_id";
      $entity_descr_field = "vsvr_label";
      $entity_table       = "netscaler_vservers";
      break;
    case "netscaler_svc":
      $entity_id_field    = "svc_id";
      $entity_descr_field = "svc_label";
      $entity_table       = "netscaler_services";
      break;
    default:
      $entity_id_field    = $entity_type."_id";
      $entity_descr_field = $entity_type."_descr";
      $entity_table       = $entity_type."s";
      break;
  }

  #echo("etype[".$entity_type."]");

  return array($entity_table, $entity_id_field, $entity_descr_field);

}


/**
 * Return an array of entities of a certain type which match device_id and entity attribute rules.
 *
 * @param integer device_id
 * @param array attributes
 * @param string entity_type
 * @return array
*/

/// FIXME - this is going to be horribly slow.

function match_device_entities($device_id, $attributes, $entity_type)
{

  $param = array();

  list($entity_table, $entity_id_field, $entity_descr_field) = entity_type_translate ($entity_type);

  $sql   = "SELECT * FROM `".mres($entity_table)."`";
  $sql  .= " WHERE device_id = ?";
  $param[] = $device_id;

  foreach($attributes as $attrib)
  {
    switch ($attrib['condition'])
    {
      case 'equals':
      case '=':
        $sql .= " AND `".$attrib['attrib']."` = ?";
        $param[] = $attrib['value'];
        break;
      case 'notequals':
      case '!=':
        $sql .= " AND `".$attrib['attrib']."` != ?";
        $param[] = $attrib['value'];
        break;
      case "greater":
      case ">":
        $sql .= " AND `".$attrib['attrib']."` > ?";
        $param[] = $attrib['value'];
        break;
      case 'match':
        $attrib['value'] = str_replace("*", "%", $attrib['value']);
        $sql .= " AND `".$attrib['attrib']."` LIKE ?";
        $param[] = $attrib['value'];
        break;
      case 'notmatch':
        $attrib['value'] = str_replace("*", "%", $attrib['value']);
        $sql .= " AND `".$attrib['attrib']."` NOT LIKE ?";
        $param[] = $attrib['value'];
        break;
    }
  }

  $entities = dbFetchRows($sql, $param);
  return $entities;

}


/**
 * Test if an entity matches a set of attributes
 * Uses a supplied device array for matching.
 *
 * @param array entity
 * @param array attributes
 * @return boolean
*/

function match_entity($entity, $attributes)
{

  #print_r($entity);
  #print_r($attributes);


  $failed  = 0;
  $success = 0;

  foreach($attributes as $attrib)
  {
    switch ($attrib['condition'])
    {
      case 'equals':
        if( mb_strtolower($entity[$attrib['attrib']]) ==  mb_strtolower($attrib['value'])) { $success++; } else { $fail++; }
        break;
      case 'match':
        $attrib['value'] = str_replace('*', '.*', $attrib['value']);
        $attrib['value'] = str_replace('?', '.',  $attrib['value']);
        if(preg_match('/^'.$attrib['value'].'$/i', $entity[$attrib['attrib']])) { $success++; } else { $fail++; }
        break;
    }
  }

  if($fail) {
    return FALSE;
  } else {
    return TRUE;
  }

}

function update_device_alert_table($device)
{

  $dbc = array();
  $alert_table = array();

  echo("Building alerts for device ".$device['hostname'].PHP_EOL);

  $conditions = cache_device_conditions($device);

  $db_cs = dbFetchRows("SELECT * FROM `alert_table` WHERE `device_id` = ?", array($device['device_id']));
  foreach($db_cs as $db_c)
  {
    $dbc[$db_c['entity_type']][$db_c['entity_id']][$db_c['alert_test_id']] = $db_c;
  }

  foreach ($conditions['assoc'] as $assoc_id => $assoc)
  {
    // Check that the entity_type matches the one we're interested in.
    echo("Matching $assoc_id (".$assoc['entity_type'].")");

    list($entity_table, $entity_id_field, $entity_descr_field) = entity_type_translate ($assoc['entity_type']);

    $alert = $conditions['cond'][$assoc['alert_test_id']];

    $entities = match_device_entities($device['device_id'], $assoc['attributes'], $assoc['entity_type']);

    foreach($entities AS $id => $entity)
    {
      $alert_table[$assoc['entity_type']][$entity[$entity_id_field]][$assoc['alert_test_id']][] = $assoc_id;
      $alert_count++;
    }

    echo(count($entities)." matched".PHP_EOL);

    echo("\n");

  }

  foreach($alert_table AS $entity_type => $entities)
  {
    foreach($entities AS $entity_id => $entity)
    {
      foreach($entity AS $alert_test_id => $b)
      {
        echo(str_pad($entity_type, "20").str_pad($entity_id, "20").str_pad($alert_test_id, "20"));
        echo(str_pad(implode($b,","), "20"));
        if(isset($dbc[$entity_type][$entity_id][$alert_test_id]))
        {
          if($dbc[$entity_type][$entity_id][$alert_test_id]['alert_assocs'] != implode($b,",")) { $update_array = array('alert_assocs' => implode($b,","));  }
          #echo("[".$dbc[$entity_type][$entity_id][$alert_test_id]['alert_assocs']."][".implode($b,",")."]");
          if(is_array($update_array))
          {
            dbUpdate($update_array, 'alert_table', '`alert_table_id` = ?', array($dbc[$entity_type][$entity_id][$alert_test_id]['alert_table_id']));
            unset($update_array); echo("U".mysql_affected_rows());
          }
          unset($dbc[$entity_type][$entity_id][$alert_test_id]);
        } else {
          $alert_table_id = dbInsert(array('device_id' => $device['device_id'], 'entity_type' => $entity_type, 'entity_id' => $entity_id, 'alert_test_id' => $alert_test_id, 'alert_assocs' => implode($b,",")), 'alert_table');
          dbInsert(array('alert_table_id' => $alert_table_id), 'alert_table-state');
          echo("insert");
        }
        echo(PHP_EOL);
      }
    }
  }

  echo("\nChecking for stale entries: ");

  foreach($dbc AS $type => $entity)
  {
    foreach($entity AS $entity_id => $alert)
    {
      foreach($alert AS $alert_test_id => $data)
      {
        dbDelete('alert_table', "`alert_table_id` =  ?", array($data['alert_table_id']));
        dbDelete('alert_table-state', "`alert_table_id` =  ?", array($data['alert_table_id']));
        echo("-");
      }
    }
  }

  echo("\n");

}


/**
 * Check all alerts for a device to see if they should be notified or not
 *
 * @param array device
 * @return NULL
 */

function process_alerts($device)
{

  global $config, $alert_rules, $alert_assoc;

  echo("Processing alerts for ".$device['hostname'].PHP_EOL);

  $alert_table = cache_device_alert_table($device['device_id']);

  $sql  = "SELECT * FROM  `alert_table`";
  $sql .= " LEFT JOIN  `alert_table-state` ON  `alert_table`.`alert_table_id` =  `alert_table-state`.`alert_table_id`";
  $sql .= " WHERE  `device_id` =  ?";

  foreach (dbFetchRows($sql, array($device['device_id'])) as $entry)
  {
    echo("Alert: ".$entry['alert_table_id']." Status: ".$entry['alert_status']);

    if($entry['alert_status'] == '0')
    {
      echo('Alert status set. ');

      // Check to see if this alert has been suppressed by anything

      // Have all alerts on the device been suppressed?
      if($device['ignore'] || ($device['ignore_until'] && $device['ignore_until'] > time() )) { $entry['suppress_alert'] = TRUE; }

      // Have all alerts on the entity been suppressed?

      // Have alerts from this alerter been suppressed?

      // Has this specific alert been suppressed?

      // Has this been alerted more frequently than the alert interval in the config?
      if((time() - $entry['last_alerted']) < $config['alerts']['interval']) { $entry['suppress_alert'] = TRUE; }


      ## FIXME -- this time should be configurable per-alert or per-entity or something
      if($entry['suppress_alert'] != TRUE)
      {
        echo('Not alerted today. ');
        $alert = $alert_rules[$entry['alert_test_id']];
        #dbFetchRow("SELECT * FROM `alert_tests` WHERE `alert_test_id` = ?", array($entry['alert_test_id']));

        #print_r($alert);
        #print_r($entry);

        $state      = unserialize($entry['state']);
        $conditions = unserialize($alert['conditions']);

        #print_r($conditions);
        #print_r($state);

        $entity = get_entity_by_id_cache($entry['entity_type'], $entry['entity_id']);
        $entity_descr = entity_descr($entry['entity_type'], $entry['entity_id']);

        $condition_text = "";
        foreach($state['failed'] AS $failed)
        {
          $condition_text .= $failed['metric'] . " " . $failed['condition'] . " ". $failed['value'] ." (". $state['metrics'][$failed['metric']].")<br />";
        }

        $graphs = ""; $metric_text = "";
        foreach($state['metrics'] AS $metric => $value)
        {
          $metric_text .= $metric ." = ".$value.PHP_EOL."<br />";
          if(is_array($config['alert_graphs'][$entry['entity_type']][$metric]))
          {
            // We can draw a graph for this type/metric pair!

            $graph_array = $config['alert_graphs'][$entry['entity_type']][$metric];
            foreach($graph_array as $key => $val)
            {
              // Check to see if we need to do any substitution
              if (substr($val,0,1)=="@")
              {
                $nval = substr($val,1);
                echo(" replaced ".$val." with ". $entity[$nval] ." from entity. ".PHP_EOL."<br />");
                $graph_array[$key] = $entity[$nval];
              }
            }

            $vars = $graph_array;
            $auth = TRUE;
            $vars['image_data_uri'] = TRUE;
            $vars['height'] = '150';
            $vars['width']  = '400';
            $_GET['legend'] = 'no';
            $vars['from']   = time();
            $vars['to']     = time()-8400;

            include('html/includes/graphs/graph.inc.php');

            $graphs .= '<img src="'.$image_data_uri.'">'."<br />";

            unset ($vars); unset($graph_array);
          }
        }

$message = '
<head>
    <title>Observium Alert</title>
<style>
.observium{ width:100%; max-width: 500px; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; border:1px solid #DDDDDD; background-color:#FAFAFA;
 font-size: 13px; color: #777777; }
.header{ font-weight: bold; font-size: 16px; padding: 5px; color: #555555; }
.red { color: #cc0000; }
#deviceinfo tr:nth-child(odd){ background: #ffffff; }
</style>
<style type="text/css"></style></head>
<body>
<table class="observium">
  <tbody>
    <tr>
      <td align="center">
        <table class="observium" id="deviceinfo">
  <tbody>
    <tr><td colspan=2 class="header">Alert</td></tr>
    <tr><td><b>Alert</b></font></td><td class="red">'.$alert['alert_message'].'</font></td></tr>
    <tr><td><b>Entity</b></font></td><td>'.generate_entity_link($entry['entity_type'], $entry['entity_id']).'</font></td></tr>
    <tr><td><b>Conditions</b></font></td><td>'.$condition_text.'</font></td></tr>
    <tr><td><b>Metrics</b></font></td><td>'.$metric_text.'</font></td></tr>
    <tr><td><b>Duration</b></font></td><td>'.formatUptime(time() - $entry['last_changed']).'</font></td></tr>
    <tr><td colspan="2" class="header">Device</td></tr>
    <tr><td><b>Device</b></font></td><td>'.generate_device_link($device).'</font></td></tr>
    <tr><td><b>Hardware</b></font></td><td>'.$device['hardware'].'</font></td></tr>
    <tr><td><b>Operating System</b></font></td><td>' . $device['os_text'] . ' ' . $device['version'] . ' ' . $device['features'] .'</font></td></tr>
    <tr><td><b>Location</b></font></td><td>'.htmlspecialchars($device['location']).'</font></td></tr>
    <tr><td><b>Uptime</b></font></td><td>'.deviceUptime($device).'</font></td></tr>
  </tbody></table>
</td></tr>
<tr><td>
<center>'.$graphs.'</center></td></tr>
</tbody></table>
</body>
</html>';

        alert_notify($device, "ALERT: [".$device['hostname']."] [".$alert['entity_type']."] [".$entity_descr."] ".$alert['alert_message'],  $message);

        $update_array['last_alerted'] = time();
        dbUpdate($update_array, 'alert_table-state', '`alert_table_id` = ?', array($entry['alert_table_id']));

      } else { echo("Already alerted this period.".(time() - $entry['last_alerted'])); }
    } elseif($entry['alert_status'] == '1') { echo("Status: OK. "); } else { echo("Unknown status."); }
      echo(PHP_EOL);
  }
}

function alert_notify($device,$title,$message)
{
  /// NOTE. Need full rewrite to universal function with message queues and multi-protocol (email,jabber,twitter)
  global $config, $debug;

  if (!$device['ignore'])
  {
    if (!get_dev_attrib($device,'disable_notify'))
    {
      if ($config['alerts']['email']['default_only'])
      {
        $email = $config['alerts']['email']['default'];
      } else {
        if (get_dev_attrib($device,'override_sysContact_bool'))
        {
          $email = get_dev_attrib($device,'override_sysContact_string');
        }
        elseif ($device['sysContact'])
        {
          $email = $device['sysContact'];
        } else {
          $email = $config['alerts']['email']['default'];
        }
      }
      $emails = parse_email($email);

      if ($emails)
      {
        // Mail backend params
        $params = array('localhost' => php_uname('n'));
        $backend = strtolower(trim($config['email_backend']));
        switch ($backend) {
          case 'sendmail':
            $params['sendmail_path'] = $config['email_sendmail_path'];
            break;
          case 'smtp':
            $params['host']     = $config['email_smtp_host'];
            $params['port']     = $config['email_smtp_port'];
            if ($config['email_smtp_secure'] == 'ssl')
            {
              $params['host']   = 'ssl://'.$config['email_smtp_host'];
              if ($config['email_smtp_port'] == 25) {
                $params['port'] = 465; // Default port for SSL
              }
            }
            $params['timeout']  = $config['email_smtp_timeout'];
            $params['auth']     = $config['email_smtp_auth'];
            $params['username'] = $config['email_smtp_username'];
            $params['password'] = $config['email_smtp_password'];
            if ($debug) { $params['debug'] = TRUE; }
            break;
          default:
            $backend = 'mail'; // Default mailer backend
        }

        // Mail headers
        $headers = array();
        if (empty($config['email_from']))
        {
          $headers['From']   = '"Observium" <observium@'.$params['localhost'].'>'; // Default "From:"
        } else {
          foreach (parse_email($config['email_from']) as $from => $from_name)
          {
            $headers['From'] = (empty($from_name)) ? $from : '"'.$from_name.'" <'.$from.'>'; // From:
          }
        }
        $rcpts_full = '';
        $rcpts = '';
        foreach ($emails as $to => $to_name)
        {
          $rcpts_full .= (empty($to_name)) ? $to.', ' : '"'.$to_name.'" <'.$to.'>, ';
          $rcpts .= $to.', ';
        }
        $rcpts_full = substr($rcpts_full, 0, -2); // To:
        $rcpts = substr($rcpts, 0, -2);
        $headers['Subject']      = $title; // Subject:
        $headers['X-Priority']   = 3; // Mail priority
        $headers['X-Mailer']     = 'Observium ' . $config['version']; // X-Mailer:
        $headers['Content-type'] = 'text/html';
        $headers['Message-ID']   = '<' . md5(uniqid(time())) . '@' . $params['localhost'] . '>';
        $headers['Date']         = date('r', time());

        // Mail body
        $message_header = $config['page_title_prefix']."\n\n";
        $message_footer = "\n\nE-mail sent to: ".$rcpts."\n";
        $message_footer .= "E-mail sent at: " . date($config['timestamp_format']) . "\n";
        $body = $message_header . $message . $message_footer;

        // Create mailer instance
        $mail =& Mail::factory($backend, $params);
        // Sending email
        $status = $mail->send($rcpts_full, $headers, $body);
        if (PEAR::isError($status)) { echo 'Mailer Error: ' . $status->getMessage() . PHP_EOL; }
      }
    }
  }
}


// EOF
