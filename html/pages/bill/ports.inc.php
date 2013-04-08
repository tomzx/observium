<?php

$res   = "";
$count = 0;
$speed = 0;
$ports = dbFetchRows("SELECT * FROM `bill_ports` AS B, `ports` AS P, `devices` AS D
                      WHERE B.bill_id = ? AND P.port_id = B.port_id
                      AND D.device_id = P.device_id", array($bill_id));

foreach ($ports as $port) {
  $emptyCheck = true;
  $count++;
  $speed += $port['ifSpeed'];

  /// FIXME - clean this up, it's horrible.

  $devicebtn = str_replace("list-device", "btn btn-mini", generate_device_link($port, ""));
  $devicebtn = str_replace("\">".$port['hostname'], "\" style=\"color: #000;\"><i class=\"icon-hdd\"></i> ".$port['hostname'], $devicebtn);

  if (empty($port['ifAlias'])) { $portalias = ""; } else { $portalias = " - ".$port['ifAlias'].""; }

  $portbtn = str_replace("interface-upup", "btn btn-mini", generate_port_link($port, "<i class='icon-random'></i> ".$port['ifName'].$portalias));
  $portbtn = str_replace("interface-updown", "btn btn-mini btn-danger", $portbtn);
  $portbtn = str_replace("interface-downdown", "btn btn-mini btn-danger", $portbtn);
  $portbtn = str_replace("interface-admindown", "btn btn-mini btn-warning disabled", $portbtn);

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
  <div class="span12 well">
    <h3 class="bill"><i class="icon-random"></i> Billed ports</h3>
    <?php echo($res); ?>
  </div>
</div>
