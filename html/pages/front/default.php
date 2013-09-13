<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @author     Dennis de Houx <info@all-in-one.be>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.9.2
 *
 */

  foreach ($config['frontpage']['order'] as $module) {
    switch ($module) {
      case "status_summary":
        include("includes/status-summary.inc.php");
        break;
      case "map":
        show_map($config);
        break;
      case "device_status_boxes":
        show_status_boxes($config);
        break;
      case "device_status":
        show_status($config);
        break;
      case "overall_traffic":
        show_traffic($config);
        break;
      case "custom_traffic":
        show_customtraffic($config);
        break;
      case "syslog":
        show_syslog($config);
        break;
      case "eventlog":
        show_eventlog($config);
        break;
      case "minigraphs":
        show_minigraphs($config);
        break;
      case "micrographs":
        show_micrographs($config);
        break;
    }
  }

function show_map($config)
{
?>
<div class="row">
  <div class="col-md-12" style="padding: 10px;">
  <script type='text/javascript' src='https://www.google.com/jsapi'></script>
  <script type='text/javascript'>
    google.load('visualization', '1.1', {'packages': ['geochart']});
    google.setOnLoadCallback(drawRegionsMap);
    function drawRegionsMap() {
    var data = new google.visualization.DataTable();
    data.addColumn('number', 'Latitude');
    data.addColumn('number', 'Longitude');
    data.addColumn('string', 'Location');
    data.addColumn('number', 'Status');
    data.addColumn('number', 'Devices');
    data.addColumn({type: 'string', role: 'tooltip'});
    data.addRows([

    <?php
      $locations_up = array();
      $locations_down = array();
      $devicesArray = array();
      foreach (dbFetchRows("SELECT * FROM devices") as $device) {
      $devicesArray[] = array("device_id" => $device['device_id'], "hostname" => $device['hostname'], "location" => $device['location'], "status" => $device['status'], "ignore" => $device['ignore'], "disabled" => $device['disabled'], "location_lat" =>  $device['location_lat'], "location_lon" =>  $device['location_lon']);
      }
      foreach (getlocations() as $location) {
      $location = addslashes($location);
      $devices = array();
      $devices_down = array();
      $devices_up = array();
      $count = 0;
      $down  = 0;
      foreach ($devicesArray as $device) {
        if ($device['location'] == $location) {
        $devices[] = $device['hostname'];
        $count++;
        if ($device['status'] == "0" && $device['disabled'] == "0" && $device['ignore'] == "0") { $down++; $devices_down[] = $device['hostname'];  $lat = $device['location_lat']; $lon = $device['location_lon'];
        } elseif ($device['status'] == "1") { $devices_up[] = $device['hostname']; $lat = $device['location_lat']; $lon = $device['location_lon']; }
        }
      }
      $count = (($count < 100) ? $count : "100");
      if ($down > 0) {
        $locations_down[]   = "[".$lat.", ".$lon.", '".$location."', ".$down.", ".$count*$down.", '".count($devices_up). " Devices OK, " . count($devices_down). " Devices DOWN: (". implode(", ", $devices_down).")']";
      } else {
        $locations_up[] = "[".$lat.", ".$lon.", '".$location."', 0, ".$count.", '".count($devices_up). " Devices UP: (". implode(", ", $devices_up).")']";
      }
      }
      unset($devicesArray);
      echo(implode(",\n", array_merge($locations_up, $locations_down)));
    ?>

    ]);
    var options = {
      region: '<?php echo $config['frontpage']['map']['region']; ?>',
      resolution: '<?php echo $config['frontpage']['map']['resolution']; ?>',
      displayMode: 'markers',
      keepAspectRatio: 0,
      width: 1160,
      height: 480,
      is3D: true,
      legend: 'none',
      enableRegionInteractivity: true,
      <?php if ($config['frontpage']['map']['realworld']) { echo "\t\t  datalessRegionColor: '#93CA76',"; } else {
                      echo "\t\t  datalessRegionColor: '#d5d5d5',"; } ?>
      <?php if ($config['frontpage']['map']['realworld']) { echo "\t\t  backgroundColor: {fill: '#cceef0'},"; } ?>
      magnifyingGlass: {enable: true, zoomFactor: 5},
      colorAxis: {values: [0, 1, 2, 3], colors: ['darkgreen', 'orange', 'orangered', 'red']},
      markerOpacity: 0.75,
      sizeAxis: {minValue: 1,  maxValue: 10, minSize: 10, maxSize: 40}
    };
    var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
    chart.draw(data, options);
    google.visualization.events.addListener(chart, 'ready', onReady);
    function onReady() {
      google.visualization.events.addListener(chart, 'select', gotoLocation);
    }
    function gotoLocation() {
      var selection = chart.getSelection();
      var item = selection[0];
      var url = '<?php echo generate_url(array("page" => "devices")); ?>';
      var location = data.getValue(item.row, 2);
      url = url+'location='+location+'/';
      window.location = url;
    }
    };
  </script>
  <div id="chart_div"></div>
  </div>
</div>
<?php

  }
  // End show_map


  function show_traffic($config) {
  // Show Traffic
    // FIXME - This is not how we do port types.

    if ($_SESSION['userlevel'] >= '5') {
    $sql  = "select * from ports as I, devices as D WHERE `ifAlias` like 'Transit:%' AND I.device_id = D.device_id ORDER BY I.ifAlias";
    $query = mysql_query($sql);
    unset ($seperator);
    while ($interface = mysql_fetch_assoc($query)) {
      $ports['transit'] .= $seperator . $interface['port_id'];
      $seperator = ",";
    }
    $sql  = "select * from ports as I, devices as D WHERE `ifAlias` like 'Peering:%' AND I.device_id = D.device_id ORDER BY I.ifAlias";
    $query = mysql_query($sql);
    unset ($seperator);
    while ($interface = mysql_fetch_assoc($query)) {
      $ports['peering'] .= $seperator . $interface['port_id'];
      $seperator = ",";
    }
    $sql  = "select * from ports as I, devices as D WHERE `ifAlias` like 'Core:%' AND I.device_id = D.device_id ORDER BY I.ifAlias";
    $query = mysql_query($sql);
    unset ($seperator);
    while ($interface = mysql_fetch_assoc($query)) {
      $ports['core'] .= $seperator . $interface['port_id'];
      $seperator = ",";
    }
    $links['transit']  = generate_url(array("page" => "iftype", "type" => "transit"));
    $links['peering']  = generate_url(array("page" => "iftype", "type" => "peering"));
    $links['peer_trans']  = generate_url(array("page" => "iftype", "type" => "peering,transit"));
    echo("<div class=\"row\">");
    echo("  <div class=\"col-md-6 \">");
    echo("    <h3 class=\"bill\">Overall Transit Traffic Today</h3>");
    echo("    <a href=\"".$links['transit']."\"><img src=\"graph.php?type=multiport_bits_separate&amp;id=".$ports['transit']."&amp;legend=no&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/></a>");
    echo("  </div>");
    echo("  <div class=\"col-md-6 \">");
    echo("    <h3 class=\"bill\">Overall Peering Traffic Today</h3>");
    echo("    <a href=\"".$links['peering']."\"><img src=\"graph.php?type=multiport_bits_separate&amp;id=".$ports['peering']."&amp;legend=no&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/></a>");
    echo("  </div>");
    echo("</div>");
    echo("<div class=\"row\">");
    echo("  <div class=\"col-md-12 \">");
    echo("    <h3 class=\"bill\">Overall Transit &amp; Peering Traffic This Month</h3>");
    echo("    <a href=\"".$links['peer_trans']."\"><img src=\"graph.php?type=multiport_bits_duo_separate&amp;id=".$ports['peering']."&amp;idb=".$ports['transit']."&amp;legend=no&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=1100&amp;height=200\"/></a>");
    echo("  </div>");
    echo("</div>");
    unset($links);
    }
  }
  // End show_traffic


  function show_customtraffic($config) {
  // Show Custom Traffic
    if ($_SESSION['userlevel'] >= '5') {
    $config['frontpage']['custom_traffic']['title'] = (empty($config['frontpage']['custom_traffic']['title']) ? "Custom Traffic" : $config['frontpage']['custom_traffic']['title']);
    echo("<div class=\"row\">");
    echo("  <div class=\"col-md-6 \">");
    echo("    <h3 class=\"bill\">".$config['frontpage']['custom_traffic']['title']." Today</h3>");
    echo("    <img src=\"graph.php?type=multiport_bits&amp;id=".$config['frontpage']['custom_traffic']['ids']."&amp;legend=no&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/>");
    echo("  </div>");
    echo("  <div class=\"col-md-6 \">");
    echo("    <h3 class=\"bill\">".$config['frontpage']['custom_traffic']['title']." This Week</h3>");
    echo("    <img src=\"graph.php?type=multiport_bits&amp;id=".$config['frontpage']['custom_traffic']['ids']."&amp;legend=no&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/>");
    echo("  </div>");
    echo("</div>");
    echo("<div class=\"row\">");
    echo("  <div class=\"col-md-12 \">");
    echo("    <h3 class=\"bill\">".$config['frontpage']['custom_traffic']['title']." This Month</h3>");
    echo("    <img src=\"graph.php?type=multiport_bits&amp;id=".$config['frontpage']['custom_traffic']['ids']."&amp;legend=no&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=1100&amp;height=200\"/>");
    echo("  </div>");
    echo("</div>");
    }
  }  // End show_customtraffic


  function show_minigraphs($config)
  {
    // Show Custom MiniGraphs
    if ($_SESSION['userlevel'] >= '5')
    {
    $minigraphs = explode(";", $config['frontpage']['minigraphs']['ids']);
    $legend = (($config['frontpage']['minigraphs']['legend'] == false) ? "no" : "yes");
    echo("<div class=\"row\">\n");
    echo("  <div class=\"col-md-12\">\n");
    if ($config['frontpage']['minigraphs']['title'])
    {
      echo("    <h3 class=\"bill\">".$config['frontpage']['minigraphs']['title']."</h3>\n");
    }

    foreach($minigraphs as $graph)
    {
      list($device, $type, $header) = explode(",", $graph, 3);
      if (strpos($type, "device") === false)
      {
      $links = generate_url(array("page" => "graphs", "type" => $type, "id" => $device));
    //, "from" => $config['time']['day'], "to" => $config['time']['now']));
      echo("    <div class=\"pull-left\"><p style=\"text-align: center; margin-bottom: 0px;\"><strong>".$header."</strong></p><a href=\"".$links."\"><img src=\"graph.php?type=".$type."&amp;id=".$device."&amp;legend=".$legend."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=215&amp;height=100\"/></a></div>\n");
      } else {
      $links = generate_url(array("page" => "graphs", "type" => $type, "device" => $device));
    //, "from" => $config['time']['day'], "to" => $config['time']['now']));
      echo("    <div class=\"pull-left\"><p style=\"text-align: center; margin-bottom: 0px;\"><strong>".$header."</strong></p><a href=\"".$links."\"><img src=\"graph.php?type=".$type."&amp;device=".$device."&amp;legend=".$legend."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=215&amp;height=100\"/></a></div>\n");
      }
    }
    unset($links);
    echo("  </div>\n");
    echo("</div>\n");
    }
  } // End show_minigraphs

  function show_micrographs($config)
  {
    echo("<!-- Show custom micrographs -->\n");
    if ($_SESSION['userlevel'] >= '5')
    {
    $width = $config['frontpage']['micrograph_settings']['width'];
    $height = $config['frontpage']['micrograph_settings']['height'];
    echo("<div class=\"row\">\n");
    echo("  <div class=\"col-md-12\">\n");
    echo("  <table class=\"table table-bordered table-condensed-more table-rounded\">\n");
    echo("    <tbody>\n");
    foreach ($config['frontpage']['micrographs'] as $row)
    {
      $micrographs = explode(";", $row['ids']);
      $legend = (($row['legend'] == false) ? "no" : "yes");
      echo("    <tr>\n");
      if ($row['title'])
      {
      echo("      <th style=\"vertical-align: middle;\">".$row['title']."</th>\n");
      }

      echo("      <td>");
      foreach($micrographs as $graph)
      {
      list($device, $type, $header) = explode(",", $graph, 3);
      if (strpos($type, "device") === false)
      {
    $which = "id";
      } else {
      $which = "device";
    }

      $links = generate_url(array("page" => "graphs", "type" => $type, $which => $device));
      echo("<div class=\"pull-left\">");
      if ($header)
      {
    echo("<p style=\"text-align: center; margin-bottom: 0px;\">".$header."</p>");
      }
      echo("<a href=\"".$links."\" style=\"margin-left: 5px\"><img src=\"graph.php?type=".$type."&amp;".$which."=".$device."&amp;legend=".$legend."&amp;width=".$width."&amp;height=".$height."\"/></a>");
      echo("</div>");
      }
      unset($links);
      echo("      </td>\n");
      echo("    </tr>\n");
    }
    echo("    </tbody>\n");
    echo("  </table>\n");
    echo("  </div>\n");
    echo("</div>\n");
    }
  } // End show_micrographs

  function show_status($config)
  {
    // Show Status
    echo("<div class=\"row\">");
    echo("  <div class=\"col-md-12\">");
    echo("    <h3 class=\"bill\">Device Alerts</h3>");
    print_status($config['frontpage']['device_status']);
    echo("  </div>");
    echo("</div>");
  } // End show_status

  function show_status_boxes($config)
  {
    // Show Status Boxes
    echo("<div class=\"row\">\n");
    echo("  <div class=\"col-md-12\">\n");
    print_status_boxes($config['frontpage']['device_status']);
    echo("  </div>\n");
    echo("</div>\n");
  } // End show_status_boxes



  function show_syslog($config) {
    // Show syslog
    $show_syslog = "<div class=\"row\">";
    $show_syslog .= "  <div class=\"col-md-12 \">";
    $show_syslog .= "    <h3 class=\"bill\">Recent Syslog Messages</h3>";
    echo $show_syslog;
    print_syslogs(array('pagesize' => $config['frontpage']['syslog']['items']));
    $show_syslog = "  </div>";
    $show_syslog .= "</div>";
    echo $show_syslog;
  } // end show_syslog


  function show_eventlog($config) {
    // Show eventlog
    $show_event = "<div class=\"row\">";
    $show_event .= "  <div class=\"col-md-12 \">";
    $show_event .= "    <h3 class=\"bill\">Recent Eventlog Entries</h3>";
    echo $show_event;
    print_events(array('pagesize' => $config['frontpage']['eventlog']['items']));
    $show_event = "  </div>";
    $show_event .= "</div>";
    echo $show_event;
  } // End show_eventlog

?>
