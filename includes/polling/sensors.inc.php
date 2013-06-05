<?php

/* Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Observium Developers - http://www.observium.org
 *
 * @package    observium
 * @subpackage poller
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

$config['sensors']['cache_oids']['netscaler-health']       = array('.1.3.6.1.4.1.5951.4.1.1.41.7.1.2');
$config['sensors']['cache_oids']['cisco-entity-sensor']    = array('.1.3.6.1.4.1.9.9.91.1.1.1.1.4');
$config['sensors']['cache_oids']['entity-sensor']          = array('.1.3.6.1.2.1.99.1.1.1.4');
$config['sensors']['cache_oids']['equallogic']             = array('.1.3.6.1.4.1.12740.2.1.6.1.3.1', '.1.3.6.1.4.1.12740.2.1.7.1.3.1');


if(dbFetchCell("SELECT COUNT(*) FROM `sensors` WHERE `device_id` = ? AND `sensor_deleted` = '0'", array($device['device_id'])) > 0)
{

  echo('Sensors: '.PHP_EOL);

  // Cache data for use by polling modules

  foreach(dbFetchRows("SELECT `sensor_type` FROM `sensors` WHERE `device_id` = ? AND `poller_type` = 'snmp' AND `sensor_deleted` = '0' GROUP BY `sensor_type`", array($device['device_id'])) AS $s_type)
  {
    if (is_array($config['sensors']['cache_oids'][$s_type['sensor_type']]))
    {
      echo('Caching: '.$s_type['sensor_type'].' ');
      foreach($config['sensors']['cache_oids'][$s_type['sensor_type']] as $oid_to_cache)
      {
        echo($oid_to_cache.' ');
        $oid_cache = snmpwalk_numericoids($device, $oid_to_cache, $oid_cache);
      }
      echo(PHP_EOL);
    }
  }

  // Call poll_sensor for each sensor type that we support.

  foreach ($config['sensor_classes'] as $sensor_class => $sensor_unit)
  {
    poll_sensor($device, $sensor_class, $sensor_unit, $oid_cache);
  }

}
?>
