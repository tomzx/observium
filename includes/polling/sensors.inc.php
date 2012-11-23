<?php

/* Observium Network Management and Monitoring System
 * Copyright (C) 2006-2011, Observium Developers - http://www.observium.org
 *
 */

// Call poll_sensor for each sensor type that we support.

foreach ($config['sensor_classes'] as $sensor_class => $sensor_unit)
{
  poll_sensor($device, $sensor_class, $sensor_unit);
}

?>
