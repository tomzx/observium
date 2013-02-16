<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @author     Dennis de Houx <info@all-in-one.be>
 * @copyright  (C) 2006 - 2012 Adam Armstrong
 * @version    1.8.4
 *
 */

    echo ("<div class='row-fluid' style='margin-top: 10px;'></div>");

    foreach ($config['frontpage']['order'] as $item=>$value) {
	switch ($value) {
	    case "map":
		show_map($config);
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
	}
    }

    function show_map($config) {
	if ($config['frontpage']['map']['show']) {
?>
<div class="row-fluid">
    <div class="span12 well" style="background-color: #cceef0; padding: 0px 0px 0px 0px;">
	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
	<script type='text/javascript'>
	    google.load('visualization', '1', {'packages': ['geochart']});
	    google.setOnLoadCallback(drawRegionsMap);
	    function drawRegionsMap() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Site');
		data.addColumn('number', 'Status');
		data.addColumn('number', 'Devices');
		data.addColumn({type: 'string', role: 'tooltip'});
		data.addRows([
		<?php
		    $locations_up = array();
		    $locations_down = array();
		    $devicesArray = array();
		    foreach (dbFetchRows("SELECT * FROM devices") as $device) {
			if (get_dev_attrib($device, 'override_sysLocation_bool')) {
			    $device['location'] = get_dev_attrib($device, 'override_sysLocation_string');
			}
			$devicesArray[] = array("device_id" => $device['device_id'], "hostname" => $device['hostname'], "location" => $device['location'], "status" => $device['status'], "ignore" => $device['ignore'], "disabled" => $device['disabled']);
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
				$devices_up[] = $device;
				$count++;
				if ($device['status'] == "0" && $device['disabled'] == "0" && $device['ignore'] == "0") { $down++; $devices_down[] = $device['hostname']." DOWN"; }
			    }
			}
			$devices_down = array_merge(array(count($devices_up). " Devices OK"), $devices_down);
			$count = (($count < 100) ? $count : "100");
			if ($down > 0) {
			    $locations_down[]   = "['".$location."', 100, ".$count.", '".implode(", ", $devices_down)."']";
			} else {
			    $locations_up[] = "['".$location."', 0, ".$count.", '".implode(", ", $devices_down)."']";
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
		    width: 1198,
		    height: 500,
		    is3D: true,
		    legend: 'none',
		    //datalessRegionColor: '#93CA76',
                    datalessRegionColor: '#93CA76',
		    backgroundColor: {fill: '#cceef0'},
		    //backgroundColor: {fill: '#cceef0', stroke: '#e5e5e5', strokeWidth: '5'},
		    magnifyingGlass: {enable: true, zoomFactor: 8},
		    colorAxis: {values: [0, 100], colors: ['green', 'red']},
		    markerOpacity: 0.50,
		    sizeAxis: {minValue: 1,  maxValue: 20, minSize: 10, maxSize: 30}
		};
		var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
		chart.draw(data, options);
	    };
	</script>
	<div id="chart_div"></div>
    </div>
</div>
<?php
	}
    }


    function show_traffic($config) {
	// Show Traffic
	if ($config['frontpage']['overall_traffic']) {
	    if ($_SESSION['userlevel'] >= '5') {
		$sql  = "select * from ports as I, devices as D WHERE `ifAlias` like 'Transit: %' AND I.device_id = D.device_id ORDER BY I.ifAlias";
		$query = mysql_query($sql);
		unset ($seperator);
		while ($interface = mysql_fetch_assoc($query)) {
		    $ports['transit'] .= $seperator . $interface['port_id'];
		    $seperator = ",";
		}
		$sql  = "select * from ports as I, devices as D WHERE `ifAlias` like 'Peering: %' AND I.device_id = D.device_id ORDER BY I.ifAlias";
		$query = mysql_query($sql);
		unset ($seperator);
		while ($interface = mysql_fetch_assoc($query)) {
		    $ports['peering'] .= $seperator . $interface['port_id'];
		    $seperator = ",";
		}
		$sql  = "select * from ports as I, devices as D WHERE `ifAlias` like 'Core: %' AND I.device_id = D.device_id ORDER BY I.ifAlias";
		$query = mysql_query($sql);
		unset ($seperator);
		while ($interface = mysql_fetch_assoc($query)) {
		    $ports['core'] .= $seperator . $interface['port_id'];
		    $seperator = ",";
		}
		$links['transit']	= generate_url(array("page" => "iftype", "type" => "transit"));
		$links['peering']	= generate_url(array("page" => "iftype", "type" => "peering"));
		$links['peer_trans']	= generate_url(array("page" => "iftype", "type" => "peering,transit"));
		echo("<div class=\"row-fluid\">");
		echo("    <div class=\"span6 well\">");
		echo("        <h3 class=\"bill\">Overall Transit Traffic Today</h3>");
		echo("        <a href=\"".$links['transit']."\"><img src=\"graph.php?type=multiport_bits&amp;id=".$ports['transit']."&amp;legend=no&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/></a>");
		echo("    </div>");
		echo("    <div class=\"span6 well\">");
		echo("        <h3 class=\"bill\">Overall Peering Traffic Today</h3>");
		echo("        <a href=\"".$links['peering']."\"><img src=\"graph.php?type=multiport_bits&amp;id=".$ports['peering']."&amp;legend=no&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/></a>");
		echo("    </div>");
		echo("</div>");
		echo("<div class=\"row-fluid\">");
		echo("    <div class=\"span12 well\">");
		echo("        <h3 class=\"bill\">Overall Transit &amp; Peering Traffic This Month</h3>");
		echo("        <a href=\"".$links['peer_trans']."\"><img src=\"graph.php?type=multiport_bits_duo&amp;id=".$ports['peering']."&amp;idb=".$ports['transit']."&amp;legend=no&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=1100&amp;height=200\"/></a>");
		echo("    </div>");
		echo("</div>");
		unset($links);
	    }
	}
    }


    function show_customtraffic($config) {
	// Show Custom Traffic
	if ($config['frontpage']['custom_traffic']['show']) {
	    if ($_SESSION['userlevel'] >= '5') {
		$config['frontpage']['custom_traffic']['title'] = (empty($config['frontpage']['custom_traffic']['title']) ? "Custom Traffic" : $config['frontpage']['custom_traffic']['title']);
		echo("<div class=\"row-fluid\">");
		echo("    <div class=\"span6 well\">");
		echo("        <h3 class=\"bill\">".$config['frontpage']['custom_traffic']['title']." Today</h3>");
		echo("        <img src=\"graph.php?type=multiport_bits&amp;id=".$config['frontpage']['custom_traffic']['ids']."&amp;legend=no&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/>");
		echo("    </div>");
		echo("    <div class=\"span6 well\">");
		echo("        <h3 class=\"bill\">".$config['frontpage']['custom_traffic']['title']." This Week</h3>");
		echo("        <img src=\"graph.php?type=multiport_bits&amp;id=".$config['frontpage']['custom_traffic']['ids']."&amp;legend=no&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=480&amp;height=100\"/>");
		echo("    </div>");
		echo("</div>");
		echo("<div class=\"row-fluid\">");
		echo("    <div class=\"span12 well\">");
		echo("        <h3 class=\"bill\">".$config['frontpage']['custom_traffic']['title']." This Month</h3>");
		echo("        <img src=\"graph.php?type=multiport_bits&amp;id=".$config['frontpage']['custom_traffic']['ids']."&amp;legend=no&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=1100&amp;height=200\"/>");
		echo("    </div>");
		echo("</div>");
	    }
	}
    }


    function show_minigraphs($config) {
	// Show Custom MiniGraphs
	if ($config['frontpage']['minigraphs']['show']) {
	    if ($_SESSION['userlevel'] >= '5') {
		$minigraphs = explode(";", $config['frontpage']['minigraphs']['ids']);
		$legend = (($config['frontpage']['minigraphs']['legend'] == false) ? "no" : "yes");
		echo("<div class=\"row-fluid\">");
		echo("    <div class=\"span12 well\">");
		echo("        <h3 class=\"bill\">Mini Graphs Overview</h3>");
		foreach($minigraphs as $graph) {
		    list($device, $type, $header) = explode(",", $graph, 3);
		    if (strpos($type, "device") === false) {
			$links = generate_url(array("page" => "graphs", "type" => $type, "id" => $device, "from" => $config['time']['day'], "to" => $config['time']['now']));
			echo("        <div class=\"pull-left\"><p style=\"text-align: center; margin-bottom: 0px;\"><strong>".$header."</strong></p><a href=\"".links."\"><img src=\"graph.php?type=".$type."&amp;id=".$device."&amp;legend=".$legend."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=215&amp;height=100\"/></a></div>");
		    } else {
			$links = generate_url(array("page" => "graphs", "type" => $type, "device" => $device, "from" => $config['time']['day'], "to" => $config['time']['now']));
			echo("        <div class=\"pull-left\"><p style=\"text-align: center; margin-bottom: 0px;\"><strong>".$header."</strong></p><a href=\"".$links."\"><img src=\"graph.php?type=".$type."&amp;device=".$device."&amp;legend=".$legend."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=215&amp;height=100\"/></a></div>");
		    }
		}
		unset($links);
		echo("    </div>");
		echo("</div>");
	    }
	}
    }


    function show_status($config) {
	// Show Status
	if ($config['frontpage']['device_status']['show']) {
	    echo("<div class=\"row-fluid\">");
	    echo("    <div class=\"span12 well\">");
	    echo("        <h3 class=\"bill\">Device status</h3>");
	    echo("        <table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">");
	    echo("            <thead>");
	    echo("                <tr>");
	    echo("                    <th>Device</th>");
	    echo("                    <th>Type</th>");
	    echo("                    <th>Status</th>");
	    echo("                    <th>Port</th>");
	    echo("                    <th>Location</th>");
	    echo("                    <th>Time Since</th>");
	    echo("                </tr>");
	    echo("            </thead>");
	    echo("            <tbody>");
	    // Show Device Status
	    if ($config['frontpage']['device_status']['devices']) {
		if ($_SESSION['userlevel'] == '10') {
		    $sql = mysql_query("SELECT * FROM `devices` WHERE `status` = '0' AND `ignore` = '0'");
		} else {
		    $sql = mysql_query("SELECT * FROM `devices` AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND D.status = '0' AND D.ignore = '0'");
		}
		while ($device = mysql_fetch_assoc($sql)) {
		    echo("                <tr>");
		    echo("                    <td>".generate_device_link($device, $device['hostname'])."</td>");
		    echo("                    <td><span class=\"badge badge-inverse\">Device</span></td>");
		    echo("                    <td><span class=\"label label-important\">Device Down</span></td>");
		    echo("                    <td>-</td>");
		    echo("                    <td>".$device['location']."</td>");
		    echo("                    <td>".deviceUptime($device, 'short')."</td>");
		    echo("                </tr>");
		}
	    }

	    // Ports Down
	    if ($config['frontpage']['device_status']['ports']) {
		if ($_SESSION['userlevel'] == '10') {
		    $sql = mysql_query("SELECT * FROM `ports` AS I, `devices` AS D WHERE I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up' AND D.ignore = '0' AND I.ignore = '0'");
		} else {
		    $sql = mysql_query("SELECT * FROM `ports` AS I, `devices` AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND  I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up' AND D.ignore = '0' AND I.ignore = '0'");
		}
		if ($config['warn']['ifdown']) {
		    while ($interface = mysql_fetch_assoc($sql)) {
			if (!$interface['deleted']) {
			    $interface = ifNameDescr($interface);
			    echo("                <tr>");
			    echo("                    <td>".generate_device_link($interface, $interface['hostname'])."</td>");
		    	    echo("                    <td><span class=\"badge badge-info\">Port</span></td>");
			    echo("                    <td><span class=\"label label-important\">Port Down</span></td>");
			    echo("                    <td>".generate_port_link($interface, $interface['label'])."</td>");
			    echo("                    <td>".$interface['location']."</td>");
			    echo("                    <td>-</td>");
			    echo("                </tr>");
			}
		    }
		}
	    }

	    // Ports Errors
	    if ($config['frontpage']['device_status']['errors']) {
		/* STILL NEED TO DO THIS
		if ($_SESSION['userlevel'] == '10') {
		    $sql = mysql_query("SELECT * FROM `ports` AS I, `devices` AS D WHERE I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up' AND D.ignore = '0' AND I.ignore = '0'");
		} else {
		    $sql = mysql_query("SELECT * FROM `ports` AS I, `devices` AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND  I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up' AND D.ignore = '0' AND I.ignore = '0'");
		}
		if ($config['warn']['ifdown']) {
		    while ($interface = mysql_fetch_assoc($sql)) {
			if (!$interface['deleted']) {
			    $interface = ifNameDescr($interface);
			    echo("                <tr>");
			    echo("                    <td>".generate_device_link($interface, $interface['hostname'])."</td>");
		    	    echo("                    <td><span class=\"badge badge-info\">Port</span></td>");
			    echo("                    <td><span class=\"label label-important\">Port Down</span></td>");
			    echo("                    <td>".generate_port_link($interface, $interface['label'])."</td>");
			    echo("                    <td>".$interface['location']."</td>");
			    echo("                    <td>-</td>");
			    echo("                </tr>");
			}
		    }
		}
		*/
	    }

	    // Services
	    if ($config['frontpage']['device_status']['services']) {
		$sql = mysql_query("SELECT * FROM `services` AS S, `devices` AS D WHERE S.device_id = D.device_id AND service_status = 'down' AND D.ignore = '0' AND S.service_ignore = '0'");
		while ($service = mysql_fetch_assoc($sql)) {
		    echo("                <tr>");
		    echo("                    <td>".generate_device_link($service, $service['hostname'])."</td>");
		    echo("                    <td><span class=\"badge\">Service</span></td>");
		    echo("                    <td><span class=\"label label-important\">Service Down</span></td>");
		    echo("                    <td>-</td>");
		    echo("                    <td>".$service['location']."</td>");
		    echo("                    <td>-</td>");
		    echo("                </tr>");
		}
	    }

	    // BGP
	    if ($config['frontpage']['device_status']['bgp']) {
		if (isset($config['enable_bgp']) && $config['enable_bgp']) {
		    if ($_SESSION['userlevel'] == '10') {
                       $sql = mysql_query("SELECT * FROM `devices` AS D, bgpPeers AS B WHERE bgpPeerAdminStatus = 'start' AND bgpPeerState != 'established' AND B.device_id = D.device_id AND D.ignore = 0");
		    } else {
                       $sql = mysql_query("SELECT * FROM `devices` AS D, bgpPeers AS B, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND  bgpPeerAdminStatus = 'start' AND bgpPeerState != 'established' AND B.device_id = D.device_id AND D.ignore = 0");
		    }
		    while ($peer = mysql_fetch_assoc($sql)) {
			echo("                <tr>");
			echo("                    <td>".generate_device_link($peer, $peer['hostname'])."</td>");
			echo("                    <td><span class=\"badge badge-warning\">BGP</span></td>");
			echo("                    <td><span class=\"label label-important\">BGP Down</span></td>");
			echo("                    <td>".$peer['bgpPeerIdentifier']."</td>");
			echo("                    <td>".$peer['location']."</td>");
			echo("                    <td>".$peer['bgpPeerRemoteAs']." ".$peer['astext']."</td>");
			echo("                </tr>");
		    }
		}
	    }

	    // Uptime
	    if ($config['frontpage']['device_status']['uptime']) {
		if (filter_var($config['uptime_warning'], FILTER_VALIDATE_FLOAT) !== FALSE && $config['uptime_warning'] > 0) {
		    if ($_SESSION['userlevel'] == '10') {
			$sql = mysql_query("SELECT * FROM `devices` AS D WHERE D.status = '1' AND D.uptime > 0 AND D.uptime < '" . $config['uptime_warning'] . "' AND D.ignore = 0");
		    } else {
			$sql = mysql_query("SELECT * FROM `devices` AS D, devices_perms AS P WHERE D.device_id = P.device_id AND P.user_id = '" . $_SESSION['user_id'] . "' AND D.status = '1' AND D.uptime > 0 AND D.uptime < '" .
			$config['uptime_warning'] . "' AND D.ignore = 0");
		    }
		    while ($device = mysql_fetch_assoc($sql)) {
			echo("                <tr>");
			echo("                    <td>".generate_device_link($device, $device['hostname'])."</td>");
			echo("                    <td><span class=\"badge badge-inverse\">Device</span></td>");
			echo("                    <td><span class=\"label label-success\">Device Rebooted</span></td>");
			echo("                    <td>-</td>");
			echo("                    <td>".$device['location']."</td>");
			echo("                    <td>".formatUptime($device['uptime'], 'short')."</td>");
			echo("                </tr>");
		    }
		}
	    }
	    echo("            </tbody>");
	    echo("        </table>");
	    echo("    </div>");
	    echo("</div>");
	}
    }


    function show_syslog($config) {
	// Show Syslog
	if ($config['frontpage']['syslog']['show']) {
	    echo("<div class=\"row-fluid\">");
	    echo("    <div class=\"span12 well\">");
	    echo("        <h3 class=\"bill\">Recent Syslog Messages</h3>");
	    echo("        <table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">");
	    echo("            <thead>");
	    echo("                <tr>");
	    echo("                    <th>Date</th>");
	    echo("                    <th>Device</th>");
	    echo("                    <th>Message</th>");
	    echo("                </tr>");
	    echo("            </thead>");
	    echo("            <tbody>");
	    $sql = "SELECT *, DATE_FORMAT(timestamp, '%D %b %T') AS date from syslog ORDER BY timestamp DESC LIMIT ".$config['frontpage']['eventlog']['items'];
	    $query = mysql_query($sql);
	    while ($entry = mysql_fetch_assoc($query)) {
		$entry = array_merge($entry, device_by_id_cache($entry['device_id']));
		include("includes/print-syslog.inc.php");
	    }
	    echo("            </tbody>");
	    echo("        </table>");
	    echo("    </div>");
	    echo("</div>");
	}
    }


    function show_eventlog($config) {
	// Show eventlog
	if ($config['frontpage']['eventlog']['show']) {
	    $show_event = "<div class=\"row-fluid\">";
	    $show_event .= "    <div class=\"span12 well\">";
	    $show_event .= "        <h3 class=\"bill\">Recent Eventlog Entries</h3>";
	    echo $show_event;

	    print_events(array('pagesize' => $config['frontpage']['eventlog']['items']));

	    $show_event = "    </div>";
	    $show_event .= "</div>";
	    echo $show_event;
	}
    }

?>
