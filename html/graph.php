<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage graphing
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

#ob_start();

function utime()
{
  $time = explode(" ", microtime());
  $usec = (double)$time[0];
  $sec = (double)$time[1];
  return $sec + $usec;
}

$start = utime();

if (isset($_GET['debug']))
{
  $debug = TRUE;
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 0);
  ini_set('log_errors', 0);
  ini_set('error_reporting', E_ALL ^ E_NOTICE);
}
else
{
  $debug = FALSE;
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  ini_set('log_errors', 0);
  ini_set('error_reporting', 0);
}

include_once("../includes/defaults.inc.php");
include_once("../config.php");
include_once("../includes/definitions.inc.php");
include($config['install_dir'] . "/includes/common.php");
include($config['install_dir'] . "/includes/dbFacile.php");
include($config['install_dir'] . "/includes/rewrites.php");
include($config['install_dir'] . "/includes/rrdtool.inc.php");
include($config['install_dir'] . "/includes/alerts.inc.php");
include($config['html_dir'] . "/includes/functions.inc.php");
include($config['html_dir'] . "/includes/authenticate.inc.php");

// Include from PEAR
set_include_path($config['install_dir'] . "/includes/pear" . PATH_SEPARATOR . get_include_path());
include($config['install_dir'] . "/includes/pear/Net/IPv4.php");
include($config['install_dir'] . "/includes/pear/Net/IPv6.php");
include($config['install_dir'] . "/includes/pear/Net/MAC.php");

// Push $_GET into $vars to be compatible with web interface naming

foreach ($_GET as $name => $value)
{
  $vars[$name] = $value;
}

include($config['html_dir'] . "/includes/graphs/graph.inc.php");

$end = utime(); $run = $end - $start;;

if($debug) { echo("<br />Runtime ".$run." secs");

}

?>
