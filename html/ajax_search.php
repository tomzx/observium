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

#$debug =1;

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
      if(isset($_POST['queryString']) || isset($_GET['queryString'])) {
        if(isset($_POST['queryString'])) {
         $queryString = mres($_POST['queryString']);
        } elseif (isset($_GET['queryString'])) {
         $queryString = mres($_GET['queryString']);
        }


         // Is the string length greater than 0?
         if(strlen($queryString) >0) {

            /// SEARCH DEVICES
            $results = dbFetchRows("SELECT * FROM `devices` WHERE `hostname` LIKE '%" . $queryString . "%' OR `location` LIKE '%" . $queryString . "%' ORDER BY hostname LIMIT 8");
            if(count($results)) {
               echo '<li class="nav-header">Devices found: '.count($results).'</li>';
               // While there are results loop through them - fetching an Object.
               // Store the category id
               $catid = 0;
               foreach($results as $result) {
                     echo('<li class="divider" style="margin: 0px;"></li>');
                     echo("<li>");
                     echo '<a href="'.generate_device_url($result).'">';
                     $image = getImage($result);
                     $name = $result['hostname'];
                     if(strlen($name) > 35) { $name = substr($name, 0, 35) . "..."; }
                     // $description = $result->desc;
                     // if(strlen($description) > 80) { $description = substr($description, 0, 80) . "..."; }

                     $num_ports = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE device_id = ?", array($result['device_id']));
                     echo('<dl class="dl-horizontal dl-search">
                             <dt>'.$image.'</dt>
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


            if(count($results)) {
               echo '<li class="nav-header">Ports found: '.count($results).'</li>';
               // While there are results loop through them - fetching an Object.
               // Store the category id
               $catid = 0;
               foreach($results as $result) {
                     echo('<li class="divider" style="margin: 0px;"></li>');
                     echo('<li>');
                     echo '<a href="'.generate_port_url($result).'">';
                     $name = $result['ifDescr'];
                     if(strlen($name) > 35) { $name = substr($name, 0, 35) . "..."; }
                     $description = $result['ifAlias'];
                     if(strlen($description) > 80) { $description = substr($description, 0, 80) . "..."; }

    /// FIXME : THIS SUCKS

    if ($result['ifAdminStatus'] == "down") { $icon = "search-port-disabled";
    } elseif ($result['ifAdminStatus'] == "up" && $result['ifOperStatus']== "down") { $icon = "search-port-down";
    } elseif ($result['ifAdminStatus'] == "up" && $result['ifOperStatus']== "lowerLayerDown") { $icon = "search-port-down";
    } elseif ($result['ifAdminStatus'] == "up" && $result['ifOperStatus']== "up") { $icon = "search-port-up"; }



                     echo('<dl style="min-height: 32px;" class="dl-horizontal dl-search">
                             <dt><img src="images/'.$icon.'.png"></img></dt>
                             <dd><h5>'.highlight_search($name).'</h5>
                                 <small>'.$result['hostname'].'<br/>'.highlight_search($description).'</small></dd>
                            </dl>');
                   }

                   echo("</a></li>");
            }


         } else {
            // Dont do anything.
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
