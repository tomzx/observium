<?php

/**
 * Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Adam Armstrong - http://www.observium.org
 *
 * @package    observium
 * @subpackage webui
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

include($config['html_dir']."/includes/alerting-navbar.inc.php");

 // Hardcode exit if user doesn't have global write permissions.
  if ($_SESSION['userlevel'] < 10)
  {
    include("includes/error-no-perm.inc.php");
    exit;
  }

// Regenerate alerts

  echo '<div class="well">';
  foreach(dbFetchRows("SELECT * FROM `devices`") AS $device)
  {
    update_device_alert_table($device);
  }
  echo '</div>';

  unset($vars['action']);



?>
