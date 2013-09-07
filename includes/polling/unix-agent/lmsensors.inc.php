<?php

global $agent_sensors;

if ($agent_data['lmsensors'] != '|')
{

$array = preg_split("/\n/", $agent_data['lmsensors'], -1, PREG_SPLIT_NO_EMPTY);

foreach($array AS $line)
{
  if (preg_match("/(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)/", $line, $data)) {}
  elseif (preg_match("/(.*):(.*)\((.*)=(.*)\)(.*)/", $line, $data)) {}
  elseif (preg_match("/(.*):(.*)/", $line, $data)) {}
  foreach ($data as $key=>$value) {
    $data[$key] = trim($value);
  }

  if (count($data) > 2) {
    preg_match('/[a-zA-Z]+$/', $data[2], $unit);

    switch (trim($unit[0])) {
      case "C":
      case "F":
        $array['class'] = "temperature";
        break;
      case "RPM":
        $array['class'] = "fanspeed";
        break;
      case "V":
        $array['class'] = "voltage";
        break;
    }

    array_shift($data); // Remove useless line
    $array['descr'] = array_shift($data); // Set Description.
    $array['current'] = preg_replace('/[^0-9\.\-]/', '', array_shift($data));

    while($value = array_shift($data))
    {
      switch($value)
      {
        case "low":
        case "high":
        case "crit":
        case "warn":
        case "hyst":
         $array[$value] = preg_replace('/[^0-9\.\-]/', '', array_shift($data));
         break;
      }
    }
  }

  if(isset($array) && isset($array['class']))
  {
    $sensors_array[$array['descr']] = $array;
  }
  unset($array);
}

foreach($sensors_array as $key => $array)
{

#   print_vars($array);

   discover_sensor($valid['sensor'], $array['class'], $device, '', $key, 'lmsensors', $array['descr'], '1', '1', $array['low'], NULL, NULL, $array['high'], $array['current'], 'agent');
   $agent_sensors[$array['class']]['lmsensors'][$key] = array('description' => $array['descr'], 'current' => $array['current'], 'index' => $key);
}

#print_r($sensors_array);

}

?>
