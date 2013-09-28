<?php

global $agent_sensors;

if ($agent_data['array'] != '|')
{
  $items = explode("\n",$agent_data['hdarray']);
  echo "hdarray: " . print_r($items);

  if (count($items))
  {
    foreach ($items as $item)
    {
      list($param,$status) = explode('=',$item,2);
      $itemcount++;
      if($status==='Ok') {
        $istatus=1;
      } else {
        $istatus=0;
      }
      echo "Status: $status istatus: $istatus";
      if ($param==='Controller Status') {
        discover_sensor($valid['sensor'], 'status', $device, '', $itemcount, 'state', "$param: $status", '1', '1', 1, NULL, 1, NULL, $istatus, 'agent');
        $agent_sensors['status']['state'][$itemcount] = array('description' => "$param: $status", 'current' => $istatus, 'index' => $itemcount);
      }
      if (preg_match("/^Drive/","$param")) {
        discover_sensor($valid['sensor'], 'status', $device, '', $itemcount, 'state', "$param: $status", '1', '1', 1, NULL, 1, NULL, $istatus, 'agent');
        $agent_sensors['status']['state'][$itemcount] = array('description' => "$param: $status", 'current' => $istatus, 'index' => $itemcount);
      }
    }
    echo "\n";
  }
}

?>
