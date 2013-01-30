<?php

/* Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Observium Developers - http://www.observium.org
 *
 * @package    observium
 * @subpackage poller
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

// Call poll_sensor for each sensor type that we support.

foreach ($config['sensor_classes'] as $sensor_class => $sensor_unit)
{
  poll_sensor($device, $sensor_class, $sensor_unit);
}

?>
