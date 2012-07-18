<?php

$ports = dbFetchRows("SELECT * FROM `bill_ports` AS B, `ports` AS P, `devices` AS D
                      WHERE B.bill_id = ? AND P.port_id = B.port_id
                      AND D.device_id = P.device_id", array($bill_id));

foreach ($ports as $port) {
  $emptyCheck = true;

  $devicebtn = str_replace("list-device", "btn", generate_device_link($port));
  $devicebtn = str_replace("overlib('", "overlib('<div style=\'border: 5px solid #e5e5e5; background: #fff; padding: 10px;\'>", $devicebtn);
  $devicebtn = str_replace("<div>',;", "</div></div>',", $devicebtn);
  $devicebtn = str_replace("\">".$port['hostname'], "\" style=\"color: #000;\"><i class=\"icon-asterisk\"></i> ".$port['hostname'], $devicebtn);

  $portbtn = str_replace("interface-upup", "btn", generate_port_link($port));
  $portbtn = str_replace("interface-updown", "btn btn-warning", $portbtn);
  $portbtn = str_replace("interface-downdown", "btn btn-warning", $portbtn);
  $portbtn = str_replace("interface-admindown", "btn btn-warning disabled", $portbtn);
  $portbtn = str_replace("overlib('", "overlib('<div style=\'border: 5px solid #e5e5e5; background: #fff; padding: 10px;\'>", $portbtn);
  $portbtn = str_replace("<div>',;", "</div></div>',", $portbtn);
  $portbtn = str_replace("\">".strtolower($port['ifName']), "\" style=\"color: #000;\"><i class=\"icon-random\"></i> ".$port['ifName']."".$portalias, $portbtn);

  $portalias = (empty($port['ifAlias']) ? "" : " - ".$port['ifAlias']."");

  $ports = "          <div class=\"btn-toolbar\">\n";
  $ports .= "            <div class=\"btn-group\">\n";
  $ports .= "              ".$devicebtn."\n";
  $ports .= "              ".$portbtn."\n";
  $ports .= "            </div>\n";
  $ports .= "          </div>\n";
}

if (!$emptyCheck) {
  $ports = "          <div class=\"alert alert-info\">\n";
  $ports .= "            <i class=\"icon-info-sign\"></i> <strong>There are no ports assigned to this bill</strong>\n";
  $ports .= "          </div>\n";
}

?>

<div class="row-fluid">
  <div class="span12 well">
    <h3 class="bill"><i class="icon-random"></i> Billed ports</h3>
    <?php echo($ports); ?>
  </div>
</div>
