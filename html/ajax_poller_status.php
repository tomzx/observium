<?php
/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage ajax
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2012 Adam Armstrong
 *
 */

if ($_GET['debug'])
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

exec("ps aux | grep -i 'poller.php' | grep -v grep", $output, $return);
if ($return == 0) {

echo('
<img style="margin-top: -2px;" src="images/loading.gif" /> Poller running');

} else {
    echo "Poller idle";
}


?>
