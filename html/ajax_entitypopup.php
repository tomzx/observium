<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage ajax
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

if (isset($_GET['debug']) && $_GET['debug'])
{
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 0);
  ini_set('log_errors', 0);
  ini_set('allow_url_fopen', 0);
  ini_set('error_reporting', E_ALL);
}

include_once("../includes/defaults.inc.php");
include_once("../config.php");
include_once("../includes/definitions.inc.php");
include_once("includes/functions.inc.php");
include_once("../includes/dbFacile.php");
include_once("../includes/common.php");

include_once("../includes/rewrites.php");
include_once("includes/authenticate.inc.php");

if (!$_SESSION['authenticated']) { echo("unauthenticated"); exit; }

$vars = $_POST;

switch($vars['entity_type'])
{
  case "port":
    if (is_numeric($vars['entity_id']) && (port_permitted($vars['entity_id'])))
    {
      $port   = get_port_by_id($vars['entity_id']);
      device_by_id_cache($port['device_id']);
      echo generate_port_popup($port);
    } else {
      echo("You are not permitted to view this port.");
    }
    exit;
    break;

  case "device":
    if (is_numeric($vars['entity_id']) && device_permitted($vars['entity_id']))
    {
      $device = device_by_id_cache($vars['entity_id']);
      echo generate_device_link_contents($device, $vars, $start, $end);
    } else {
      echo("You are not permitted to view this device.");
    }
    exit;
    break;

  default:
    echo("Unknown entity type.");
    exit;
    break;
}

?>
