<?php

function generate_front_box ($type, $content) {
 echo("<div style='float: left; padding: 5px; width: 135px; margin: 0px;'>
  <b class='box-".$type."'>
  <b class='box-".$type."1'><b></b></b>
  <b class='box-".$type."2'><b></b></b>
  <b class='box-".$type."3'></b>
  <b class='box-".$type."4'></b>
  <b class='box-".$type."5'></b></b>
  <div class='box-".$type."fg' style='height: 90px;'>
   ".$content."
  </div>
  <b class='box-".$type."'>
  <b class='box-".$type."5'></b>
  <b class='box-".$type."4'></b>
  <b class='box-".$type."3'></b>
  <b class='box-".$type."2'><b></b></b>
  <b class='box-".$type."1'><b></b></b></b>
 </div>");
}


echo("<div style='width: 875px; float: left; padding: 3px 10px; background: #fff;'>");

$nodes = array();

$sql = mysql_query("SELECT * FROM `devices` AS D, `devices_attribs` AS A WHERE D.status = '1' AND A.device_id = D.device_id AND A.attrib_type = 'uptime' AND A.attrib_value > '0' AND A.attrib_value < '86400'");

while($device = mysql_fetch_array($sql)){
  unset($already);
  $i = 0;
  while ($i <= count($nodes)) {
    $thisnode = $device['device_id'];
    if ($nodes[$i] == $thisnode) {
     $already = "yes";
    }
    $i++;
  }
  if(!$already) { $nodes[] = $device['device_id']; }


$sql = mysql_query("SELECT * FROM `devices` WHERE `status` = '0' AND `ignore` = '0'");
while($device = mysql_fetch_array($sql)){

      echo("<div style='border: solid 2px #d0D0D0; float: left; padding: 5px; width: 120px; height: 90px; background: #ffbbbb; margin: 4px;'>
      <center><strong>".generatedevicelink($device, shorthost($device['hostname']))."</strong><br />
      <span style='font-size: 14px; font-weight: bold; margin: 5px; color: #c00;'>Device Down</span> 
      <span class=body-date-1>".truncate($device['location'], 20)."</span>
      </center></div>");


}

$sql = mysql_query("SELECT * FROM `interfaces` AS I, `devices` AS D WHERE I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up' AND D.ignore = '0' AND I.ignore = '0'");
while($interface = mysql_fetch_array($sql)){

  generate_front_box("warn", "<center><strong>".generatedevicelink($interface, shorthost($interface['hostname']))."</strong><br />
      <span style='font-size: 14px; font-weight: bold; margin: 5px; color: #c00;'>Port Down</span>
      <strong>".generateiflink($interface, makeshortif($interface['ifDescr']))."</strong> <br />
      <span class=body-date-1>".truncate($interface['ifAlias'], 20)."</span>
      </center>");

}

$sql = mysql_query("SELECT * FROM `services` AS S, `devices` AS D WHERE S.service_host = D.device_id AND service_status = 'down' AND D.ignore = '0' AND S.service_ignore = '0'");
while($service = mysql_fetch_array($sql)){


      generate_front_box("alert", "<center><strong>".generatedevicelink($service, shorthost($service['hostname']))."</strong><br />
      <span style='font-size: 14px; font-weight: bold; margin: 5px; color: #c00;'>Service Down</span> 
      <strong>".$service['service_type']."</strong><br />
      <span class=body-date-1>".truncate($interface['ifAlias'], 20)."</span>
      </center>");

}

$sql = mysql_query("SELECT * FROM `devices` AS D, bgpPeers AS B WHERE bgpPeerState != 'established' AND B.device_id = D.device_id");
while($peer = mysql_fetch_array($sql)){

  generate_front_box("alert", "<center><strong>".generatedevicelink($peer, shorthost($peer['hostname']))."</strong><br />
      <span style='font-size: 14px; font-weight: bold; margin: 5px; color: #c00;'>BGP Down</span> 
      <strong>".$peer['bgpPeerIdentifier']."</strong> <br />
      <span class=body-date-1>AS".$peer['bgpPeerRemoteAs']." ".truncate($peer['astext'], 10)."</span>
      </center>");

}

$sql = mysql_query("SELECT * FROM `devices` AS D, devices_attribs AS A WHERE A.device_id = D.device_id AND A.attrib_type = 'uptime' AND A.attrib_value < '84600'");
while($device = mysql_fetch_array($sql)){


   generate_front_box("info", "<center><strong>".generatedevicelink($device, shorthost($device['hostname']))."</strong><br />
      <span style='font-size: 14px; font-weight: bold; margin: 5px; color: #090;'>Device<br />Rebooted</span><br />
      <span class=body-date-1>".formatUptime($device['attrib_value'])."</span>
      </center>");

}


if($config['frontpage_display'] == 'syslog') {

  ## Open Syslog Div
  echo("<div style='margin: 4px; clear: both; padding: 5px;'>  
    <h3>Recent Syslog Messages</h3>
  ");

  $sql = "SELECT *, DATE_FORMAT(datetime, '%D %b %T') AS date from syslog ORDER BY datetime DESC LIMIT 20";
  $query = mysql_query($sql);
  echo("<table cellspacing=0 cellpadding=2 width=100%>");
  while($entry = mysql_fetch_array($query)) { include("includes/print-syslog.inc"); }
  echo("</table>");

  echo("</div>"); ## Close Syslog Div

} else {

  ## Open eventlog Div
  echo("<div style='margin: 4px; clear: both; padding: 5px;'>
    <h3>Recent Eventlog Entries</h3>
  ");

if($_SESSION['userlevel'] == '10') {
  $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %T') as humandate  FROM `eventlog` ORDER BY `datetime` DESC LIMIT 0,15";
} else {
  $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %T') as humandate  FROM `eventlog` AS E, devices_perms AS P WHERE E.host =
  P.device_id AND P.user_id = " . $_SESSION['user_id'] . " ORDER BY `datetime` DESC LIMIT 0,15";
}

$data = mysql_query($query);

echo("<table cellspacing=0 cellpadding=1 width=100%>");

while($entry = mysql_fetch_array($data)) {
  include("includes/print-event.inc");
}

echo("</table>");
 

  echo("</div>"); ## Close Syslog Div


}

echo("</div>");

echo("<div style='width: 290px; margin: 7px; float: right;'>
  <b class='content-box'>
  <b class='content-box1'><b></b></b>
  <b class='content-box2'><b></b></b>
  <b class='content-box3'></b>
  <b class='content-box4'></b>
  <b class='content-box5'></b></b>

  <div class='content-boxfg' style='padding: 2px 8px;'>");

/// this stuff can be customised to show whatever you want....

if($_SESSION['userlevel'] >= '5') {

  $sql  = "select * from interfaces as I, devices as D WHERE `ifAlias` like 'L2TP: %' AND I.device_id = D.device_id AND D.hostname LIKE '%";
  $sql .= $config['mydomain'] . "' ORDER BY I.ifAlias";
  $query = mysql_query($sql);
  unset ($seperator);
  while($interface = mysql_fetch_array($query)) {
    $interfaces['l2tp'] .= $seperator . $interface['interface_id'];
    $seperator = ",";
  }

  $sql  = "select * from interfaces as I, devices as D WHERE `ifAlias` like 'Transit: %' AND I.device_id = D.device_id AND D.hostname LIKE '%";
  $sql .= $config['mydomain'] . "' ORDER BY I.ifAlias";
  $query = mysql_query($sql);
  unset ($seperator);
  while($interface = mysql_fetch_array($query)) {
    $interfaces['transit'] .= $seperator . $interface['interface_id'];
    $seperator = ",";
  }

  $sql  = "select * from interfaces as I, devices as D WHERE `ifAlias` like 'Server: thlon-pbx%' AND I.device_id = D.device_id AND D.hostname LIKE '%";
  $sql .= $config['mydomain'] . "' ORDER BY I.ifAlias";
  $query = mysql_query($sql);
  unset ($seperator);
  while($interface = mysql_fetch_array($query)) {
    $interfaces['voip'] .= $seperator . $interface['interface_id'];
    $seperator = ",";
  }

  if($interfaces['transit']) {
    echo("<a onmouseover=\"return overlib('<img src=\'graph.php?type=multi_bits&interfaces=".$interfaces['transit'].
    "&from=".$day."&to=".$now."&width=400&height=150\'>', CENTER, LEFT, FGCOLOR, '#e5e5e5', BGCOLOR, '#e5e5e5', WIDTH, 400, HEIGHT, 250);\" onmouseout=\"return nd();\"  >".
    "<div style='font-size: 18px; font-weight: bold;'>Internet Transit</div>".
    "<img src='graph.php?type=multi_bits&interfaces=".$interfaces['transit'].
    "&from=".$day."&to=".$now."&width=200&height=100'></a>");
  }

  if($interfaces['l2tp']) {
    echo("<a onmouseover=\"return overlib('<img src=\'graph.php?type=multi_bits&interfaces=".$interfaces['l2tp'].
    "&from=".$day."&to=".$now."&width=400&height=150\'>', LEFT, FGCOLOR, '#e5e5e5', BGCOLOR, '#e5e5e5', WIDTH, 400, HEIGHT, 250);\" onmouseout=\"return nd();\"  >".
    "<div style='font-size: 18px; font-weight: bold;'>L2TP ADSL</div>".
    "<img src='graph.php?type=multi_bits&interfaces=".$interfaces['l2tp'].
    "&from=".$day."&to=".$now."&width=200&height=100'></a>");
  }

  if($interfaces['voip']) {
    echo("<a onmouseover=\"return overlib('<img src=\'graph.php?type=multi_bits&interfaces=".$interfaces['voip'].
    "&from=".$day."&to=".$now."&width=400&height=150\'>', LEFT, FGCOLOR, '#e5e5e5', BGCOLOR, '#e5e5e5', WIDTH, 400, HEIGHT, 250);\" onmouseout=\"return nd();\"  >".
    "<div style='font-size: 18px; font-weight: bold;'>VoIP to PSTN</div>".
    "<img src='graph.php?type=multi_bits&interfaces=".$interfaces['voip'].
    "&from=".$day."&to=".$now."&width=200&height=100'></a>");
  }

}

echo("</div>

  <b class='content-box'>
  <b class='content-box5'></b>
  <b class='content-box4'></b>
  <b class='content-box3'></b>
  <b class='content-box2'><b></b></b>
  <b class='content-box1'><b></b></b></b>
</div>
");

#echo("</div>");

/// END VOSTRON

}

?>
