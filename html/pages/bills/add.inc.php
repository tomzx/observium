<?php

$pagetitle[]      = "Add a new bill";
$links['this']    = generate_url($vars);
$links['bills']   = generate_url(array('page' => 'bills'));

?>

<div class="tabBox">
  <ul class="nav-tabs tabs" id="addBillTab">
    <li class="active"><a href="#properties" data-toggle="tab">Bill Properties</a></li>
<?php

if ($_SESSION['userlevel'] == "10") {
  if (is_numeric($vars['port'])) {
    $port = dbFetchRow("SELECT * FROM `ports` AS P, `devices` AS D WHERE `port_id` = ? AND D.device_id = P.device_id", array($vars['port']));
    echo("    <li><a href=\"#ports\" data-toggle=\"tab\">Billed Ports</a></li>\n");
  }

?>
  </ul>
  <div class="tabcontent tab-content" id="addBillTabContent" style="min-height: 50px; padding-bottom: 18px;">
    <div class="tab-pane fade active in" id="properties">
      <form name="form1" method="post" action="<?php echo($links['bills']); ?>" class="form-horizontal">
        <input type="hidden" name="addbill" value="yes">
        <script type="text/javascript">
          function billType() {
            $('#cdrDiv').toggle();
            $('#quotaDiv').toggle();
          }
        </script>
        <fieldset>
          <legend>Bill Properties</legend>
          <div class="control-group">
            <label class="control-label" for="bill_name"><strong>Description</strong></label>
            <div class="controls">
              <input class="span4" type="text" name="bill_name" value="<?php echo($port['port_descr_descr']); ?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="bill_type"><strong>Billing Type</strong></label>
            <div class="controls">
              <input type="radio" style="margin-bottom: 8px;" name="bill_type" value="cdr" checked onchange="javascript: billType();" /> CDR 95th
              <input type="radio" style="margin-bottom: 8px;" name="bill_type" value="quota" onchange="javascript: billType();" /> Quota
              <div id="cdrDiv">
                <input class="span1" type="text" name="bill_cdr">
                <select name="bill_cdr_type" style="width: 233px;">
                  <option value="Kbps">Kilobits per second (Kbps)</option>
                  <option value="Mbps" selected>Megabits per second (Mbps)</option>
                  <option value="Gbps">Gigabits per second (Gbps)</option>
                </select>
              </div>
              <div id="quotaDiv" style="display: none">
                <input class="span1" type="text" name="bill_quota">
                <select name="bill_quota_type" style="width: 233px;">
                  <option value="MB">Megabytes (MB)</option>
                  <option value="GB" selected>Gigabytes (GB)</option>
                  <option value="TB">Terabytes (TB)</option>
                </select>
              </div>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="bill_day"><strong>Billing Day</strong></label>
            <div class="controls">
              <select name="bill_day" style="width: 60px;">
<?php

for ($x=1;$x<32;$x++) {
  echo("                <option value=\"".$x."\">".$x."</option>\n");
}

?>
              </select>
            </div>
          </div>
        </fieldset>
        <fieldset>
          <legend>Optional Information</legend>
          <div class="control-group">
            <label class="control-label" for="bill_custid"><strong>Customer&nbsp;Reference</strong></label>
            <div class="controls">
              <input class="span4" type="text" name="bill_custid">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="bill_ref"><strong>Billing Reference</strong></label>
            <div class="controls">
              <input class="span4" type="text" name="bill_ref" value="<?php echo($port['port_descr_circuit']); ?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="bill_notes"><strong>Notes</strong></label>
            <div class="controls">
              <input class="span4" type="text" name="bill_notes" value="<?php echo($port['port_descr_speed']); ?>">
            </div>
          </div>
        </fieldset>
        <div class="form-actions">
          <!-- <button class="btn btn-info" style="float: right;"><i class="icon-circle-arrow-left icon-white"></i> <strong>Back to bills</strong></button> //-->
          <button type="submit" class="btn btn-primary"><i class="icon-ok-sign icon-white"></i> <strong>Add Bill</strong></button>
        </div>
    </div>
    <div class="tab-pane fade in" id="ports">
<?php

  if(is_array($port)) {
    $devicebtn = str_replace("list-device", "btn", generate_device_link($port));
    $portbtn   = str_replace("interface-upup", "btn", generate_port_link($port));
    $portalias = (empty($port['ifAlias']) ? "" : " - ".$port['ifAlias']."");
    $devicebtn = str_replace("\">".$port['hostname'], "\" style=\"color: #000;\"><i class=\"icon-asterisk\"></i> ".$port['hostname'], $devicebtn);
    $devicebtn = str_replace("overlib('", "overlib('<div style=\'border: 5px solid #e5e5e5; background: #fff; padding: 10px;\'>", $devicebtn);
    $devicebtn = str_replace("<div>',;", "</div></div>',", $devicebtn);
    $portbtn   = str_replace("\">".strtolower($port['ifName']), "\" style=\"color: #000;\"><i class=\"icon-random\"></i> ".$port['ifName']."".$portalias, $portbtn);
    $portbtn   = str_replace("overlib('", "overlib('<div style=\'border: 5px solid #e5e5e5; background: #fff; padding: 10px;\'>", $portbtn);
    $portbtn   = str_replace("<div>',;", "</div></div>',", $portbtn);
    echo("      <fieldset>\n");
    echo("        <legend>Bill Ports</legend>\n");
    echo("        <input type=\"hidden\" name=\"port\" value=\"".$port['port_id']."\">\n");
    echo("        <div class=\"control-group\">\n");
    echo("          <div class=\"btn-toolbar\">\n");
    echo("            <div class=\"btn-group\">\n");
    echo("              ".$devicebtn."\n");
    echo("              ".$portbtn."\n");
    echo("            </div>\n");
    echo("          </div>\n");
    echo("        </div>\n");
    echo("      </fieldset>\n");
  }

?>
  </div>
</div>
      </form>

<?php

  } else {
    echo("<div class=\"alert alert-error\"><i class=\"icon-warning-sign\"></i> <strong>Error!</strong><br />You don't have administration rights to create a new bill.</div>");
  }

?>
