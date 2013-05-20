<?php

$res   = "";
$count = 0;
$speed = 0;
$port_ids = dbFetchRows("SELECT `port_id` FROM `bill_ports` WHERE bill_id = ?", array($bill_id));

foreach ($port_ids AS $port_entry) {

  $port   = get_port_by_id($port_entry['port_id']);
  $device = device_by_id_cache($port['device_id']);

  $emptyCheck = true;
  $count++;
  $speed += $port['ifSpeed'];

  /// FIXME - clean this up, it's horrible.

  $devicebtn = '<button class="btn"><i class="oicon-servers"></i> '.generate_device_link($device).'</button>';

  if (empty($port['ifAlias'])) { $portalias = ""; } else { $portalias = " - ".$port['ifAlias'].""; }

  $portbtn = '<button class="btn">'.generate_port_link($port, '<i class="oicon-network-ethernet"></i> '.$port['label'].$portalias).'</button>';

#  $portbtn = str_replace("interface-upup", "btn btn-mini", generate_port_link($port, "<i class='icon-random'></i> ".$port['label'].$portalias));
#  $portbtn = str_replace("interface-updown", "btn btn-mini btn-danger", $portbtn);
#  $portbtn = str_replace("interface-downdown", "btn btn-mini btn-danger", $portbtn);
#  $portbtn = str_replace("interface-admindown", "btn btn-mini btn-warning disabled", $portbtn);

  $res    .= "          <div class=\"btn-toolbar\">\n";
  $res    .= "            <div class=\"btn-group\">\n";
  $res    .= "              ".$devicebtn."\n";
  $res    .= "              ".$portbtn."\n";
  $res    .= "            </div>\n";
  $res    .= "          </div>\n";
}

if (!$emptyCheck) {
  $res     = "          <div class=\"alert alert-info\">\n";
  $res    .= "            <i class=\"icon-info-sign\"></i> <strong>There are no ports assigned to this bill</strong>\n";
  $res    .= "          </div>\n";
}

$ports_info = array("ports" => $count, "capacity" => $speed);

?>

<div class="row-fluid">
  <div class="span12 well info_box">
    <div id="title"><i class="oicon-network-ethernet"></i> Billed Ports</div>
    <div id="content">
      <?php echo($res); ?>
    </div>
  </div>
</div>
