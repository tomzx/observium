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

// Is there a posted query string?
if (isset($_POST['queryString']) || isset($_GET['queryString']))
{
  if (isset($_POST['queryString']))
  {
   $queryString = mres($_POST['queryString']);
  } elseif (isset($_GET['queryString'])) {
   $queryString = mres($_GET['queryString']);
  }

  // Is the string length greater than 0?
  if (strlen($queryString) >0)
  {
    $found = 0;

    /// SEARCH DEVICES
    $results = dbFetchRows("SELECT * FROM `devices` WHERE `hostname` LIKE '%" . $queryString . "%' OR `location` LIKE '%" . $queryString . "%' ORDER BY hostname LIMIT 8");
    if (count($results))
    {
      $found = 1;
      echo '<li class="nav-header">Devices found: '.count($results).'</li>';

      foreach ($results as $result)
      {
        echo('<li class="divider" style="margin: 0px;"></li>');
        echo("<li>");
        echo '<a href="'.generate_device_url($result).'">';
        humanize_device($result);

        $name = $result['hostname'];
        if (strlen($name) > 35) { $name = substr($name, 0, 35) . "..."; }
        // $description = $result->desc;
        // if (strlen($description) > 80) { $description = substr($description, 0, 80) . "..."; }

        $num_ports = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE device_id = ?", array($result['device_id']));
        echo('<dl style="border-left: 10px solid '.$result['html_tab_colour'].'; " class="dl-horizontal dl-search">
                <dt style="padding-left: 10px; text-align: center;">'.$result['icon'].'</dt>
                  <dd><h5>'.highlight_search($name).'</h5>
                    <small>'.$result['hardware'].' | '.$config['os'][$result['os']]['text'].' '. $result['version'] .'
                    <br /> '.highlight_search($result['location']).' | '.$num_ports.'ports</small></dd>
                </dl>');
      }

      echo("</a></li>");
    }

    /// SEARCH PORTS
    $results = dbFetchRows("SELECT * FROM `ports` ".
                           "LEFT JOIN `devices` ON  `ports`.`device_id` =  `devices`.`device_id` ".
                           "WHERE `ifAlias` LIKE '%" . $queryString . "%' OR `ifDescr` LIKE '%" . $queryString . "%' ORDER BY ifDescr LIMIT 8");

    if (count($results))
    {
      $found = 1;
      echo '<li class="nav-header">Ports found: '.count($results).'</li>';

      foreach ($results as $result)
      {
        echo('<li class="divider" style="margin: 0px;"></li>');
        echo('<li>');
        echo '<a href="'.generate_port_url($result).'">';
        $name = $result['ifDescr'];
        if (strlen($name) > 35) { $name = substr($name, 0, 35) . "..."; }
        $description = $result['ifAlias'];
        if (strlen($description) > 80) { $description = substr($description, 0, 80) . "..."; }

         /// FIXME : THIS SUCKS
         if ($result['ifAdminStatus'] == "down") { $icon = "search-port-disabled"; $tab_colour = '#009900'; // FIXME: Why green for ignore? Also see humanize_device()
         } elseif ($result['ifAdminStatus'] == "up" && $result['ifOperStatus']== "down") { $icon = "search-port-down"; $tab_colour = '#AAAAAA';
         } elseif ($result['ifAdminStatus'] == "up" && $result['ifOperStatus']== "lowerLayerDown") { $icon = "search-port-down"; $tab_colour = '#AAAAAA';
         } elseif ($result['ifAdminStatus'] == "up" && $result['ifOperStatus']== "up") { $icon = "search-port-up"; $tab_colour = '#194B7F'; // FIXME: This colour pulled from functions.inc.php humanize_device, maybe set it centrally in definitions?
         }

         echo('<dl style="border-left: 10px solid '.$tab_colour.'; " class="dl-horizontal dl-search">
                <dt style="padding-left: 10px; text-align: center;">
                  <img src="images/'.$icon.'.png" /></dt>
                <dd><h5>'.highlight_search($name).'</h5>
                     <small>'.$result['hostname'].'<br />'.highlight_search($description).'</small></dd>
                </dl>');

       }

       echo("</a></li>");
     }

    /// SEARCH SENSORS
    $results = dbFetchRows("SELECT * FROM `sensors` ".
                           "LEFT JOIN `devices` ON  `sensors`.`device_id` =  `devices`.`device_id` ".
                           "WHERE `sensor_descr` LIKE '%" . $queryString . "%' ORDER BY sensor_descr LIMIT 8");

    if (count($results))
    {
      $found = 1;
      echo '<li class="nav-header">Sensors found: '.count($results).'</li>';

      foreach ($results as $result)
      {
        echo('<li class="divider" style="margin: 0px;"></li>');
        echo('<li>');
        echo '<a href="graphs/type=sensor_'  . $result['sensor_class'] . '/id=' . $result['sensor_id'] . '/">';
        $name = $result['sensor_descr'];
        if (strlen($name) > 35) { $name = substr($name, 0, 35) . "..."; }

        /// FIXME: once we have alerting, colour this to the sensor's status
        $tab_colour = '#194B7F'; // FIXME: This colour pulled from functions.inc.php humanize_device, maybe set it centrally in definitions?

        echo('<dl style="border-left: 10px solid '.$tab_colour.'; " class="dl-horizontal dl-search">
                <dt style="padding-left: 10px; text-align: center;">
                  <i class="'.$config['sensor_types'][$result['sensor_class']]['icon'].'"></i></dt>
                <dd><h5>'.highlight_search($name).'</h5>
                     <small>'.$result['hostname'].'<br />
                     '.$result['location'] . ' | ' .ucfirst($result['sensor_class']).' sensor</small></dd>
                </dl>');
      }

      echo("</a></li>");
    }

    if (!$found)
    {
      echo '<li class="nav-header">No search results.</li>';
    }      
  } // There is a queryString.
} else {
   echo 'There should be no direct access to this script!';
}

function highlight_search($text)
{
   global $queryString;

   return preg_replace("/".preg_quote($queryString, "/")."/i", "<em class=text-error>$0</em>", $text);
}
?>
