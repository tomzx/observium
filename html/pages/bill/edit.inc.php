<?php

include("includes/javascript-interfacepicker.inc.php");

// This needs more verification. Is it already added? Does it exist?

$base = $config["billing"]["base"];
$url  = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'edit'));

if ($bill_data['bill_type'] == "quota") {
  $data = $bill_data['bill_quota'];
  $tmp['mb'] = $data / $base / $base;
  $tmp['gb'] = $data / $base / $base / $base;
  $tmp['tb'] = $data / $base / $base / $base / $base;
  if (($tmp['tb']>1) and ($tmp['tb']<$base)) { $quota =  array("type" => "tb", "select_tb" => " selected", "data" => $tmp['tb']); }
  elseif (($tmp['gb']>1) and ($tmp['gb']<$base)) { $quota = array("type" => "gb", "select_gb" => " selected", "data" => $tmp['gb']); }
  elseif (($tmp['mb']>1) and ($tmp['mb']<$base)) { $quota = array("type" => "mb", "select_mb" => " selected", "data" => $tmp['mb']); }
}
if ($bill_data['bill_type'] == "cdr") {
  $data = $bill_data['bill_cdr'];
  $tmp['kbps'] = $data / $base;
  $tmp['mbps'] = $data / $base / $base;
  $tmp['gbps'] = $data / $base / $base / $base;
  if ($tmp['gbps']>1 and ($tmp['gbps']<$base)) { $cdr =  array("type" => "gbps", "select_gbps" => " selected", "data" => $tmp['gbps']); }
  elseif (($tmp['mbps']>1) and ($tmp['mbps']<$base)) { $cdr = array("type" => "mbps", "select_mbps" => " selected", "data" => $tmp['mbps']); }
  elseif (($tmp['kbps']>1) and ($tmp['kbps']<$base)) { $cdr = array("type" => "kbps", "select_kbps" => " selected", "data" => $tmp['kbps']); }
}

?>

<div class="row-fluid">
  <div class="span6 well info_box">
    <div id="title"><i class="oicon-wrench"></i> Bill Properties</div>
    <div id="content">
      <form id="edit" name="edit" method="post" action="<?php echo($url); ?>" class="form-horizontal">
        <input type="hidden" name="action" value="update_bill">
        <script type="text/javascript">
          function billType() {
            $('#cdrDiv').toggle();
            $('#quotaDiv').toggle();
          }
        </script>
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="bill_name"><strong>Description</strong></label>
            <div class="controls">
              <input class="span4" type="text" name="bill_name" value="<?php echo $bill_data["bill_name"]; ?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="bill_type"><strong>Billing Type</strong></label>
            <div class="controls">
              <input type="radio" style="margin-bottom: 8px;" name="bill_type" value="cdr" onchange="javascript: billType();" <?php if ($bill_data['bill_type'] == "cdr") { echo('checked '); } ?>/> CDR / 95th percentile
              <input type="radio" style="margin-bottom: 8px;" name="bill_type" value="quota" onchange="javascript: billType();" <?php if ($bill_data['bill_type'] == "quota") { echo('checked '); } ?>/> Quota
            <!--
              <div class="btn-group" data-toggle="buttons-radio" style="margin-bottom: 5px;">
                <button class="btn btn-small btn-primary<?php if ($bill_data['bill_type'] == "cdr") { echo(' active'); } ?>" name="bill_type" value="cdr" onclick="javascript: billType();">CDR / 95th percentile</button>
                <button class="btn btn-small btn-primary<?php if ($bill_data['bill_type'] == "quota") { echo(' active'); } ?>" name="bill_type" value="quota" onclick="javascript: billType();">Quota</button>
              </div>
            //-->
              <div id="cdrDiv"<?php if ($bill_data['bill_type'] == "quota") { echo(' style="display: none"'); } ?>>
                <input class="span1" type="text" name="bill_cdr" value="<?php echo $cdr['data']; ?>">
                <select name="bill_cdr_type" style="width: 233px;">
                  <option value="Kbps"<?php echo $cdr['select_kbps']; ?>>Kilobits per second (Kbps)</option>
                  <option value="Mbps"<?php echo $cdr['select_mbps']; ?>>Megabits per second (Mbps)</option>
                  <option value="Gbps"<?php echo $cdr['select_gbps']; ?>>Gigabits per second (Gbps)</option>
                </select>
              </div>
              <div id="quotaDiv"<?php if ($bill_data['bill_type'] == "cdr") { echo(' style="display: none"'); } ?>>
                <input class="span1" type="text" name="bill_quota" value="<?php echo $quota['data']; ?>">
                <select name="bill_quota_type" style="width: 233px;">
                  <option value="MB"<?php echo $quota['select_mb']; ?>>Megabytes (MB)</option>
                  <option value="GB"<?php echo $quota['select_gb']; ?>>Gigabytes (GB)</option>
                  <option value="TB"<?php echo $quota['select_tb']; ?>>Terabytes (TB)</option>
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
  $select = (($bill_data['bill_day'] == $x) ? " selected" : "");
  echo("              <option value=\"".$x."\"".$select.">".$x."</option>\n");
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
              <input class="span4" type="text" name="bill_custid" value="<?php echo $bill_data['bill_custid']; ?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="bill_ref"><strong>Billing Reference</strong></label>
            <div class="controls">
              <input class="span4" type="text" name="bill_ref" value="<?php echo $bill_data['bill_ref']; ?>" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="bill_notes"><strong>Notes</strong></label>
            <div class="controls">
              <input class="span4" type="text" name="bill_notes" value="<?php echo $bill_data['bill_notes']; ?>" />
            </div>
          </div>
        </fieldset>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" name="Submit" value="Save" /><i class="icon-ok icon-white"></i> <strong>Save Properties</strong></button>
        </div>
      </form>
    </div>
  </div>

  <div class="span6 well info_box">
    <div id="title"><i class="oicon-network-ethernet"></i> Bill Ports</div>
    <div id="content">

    <form class="form-horizontal">
      <fieldset>
        <div class="control-group">
<?php


$port_ids = dbFetchRows("SELECT `port_id` FROM `bill_ports` WHERE bill_id = ?", array($bill_id));

if (is_array($ports))
{
  foreach ($port_ids AS $port_entry) {

  $emptyCheck = true;

  $port   = get_port_by_id($port_entry['port_id']);
  $device = device_by_id_cache($port['device_id']);


    $devicebtn = '<button class="btn"><i class="oicon-servers"></i> '.generate_device_link($device).'</button>';
    if (empty($port['ifAlias'])) { $portalias = ""; } else { $portalias = " - ".$port['ifAlias'].""; }
    $portbtn = '<button class="btn">'.generate_port_link($port, '<i class="oicon-network-ethernet"></i> '.$port['label'].$portalias).'</button>';

    $delete  = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'edit', 'action' => 'delete_bill_port', 'port_id' => $port['port_id']));

//    echo("          <form action=\"\" method=\"post\" name=\"delete".$port['port_id']."\" style=\"display: none;\">\n");
    echo("          <form action=\"".$url."\" method=\"get\" name=\"delete".$port['port_id']."\">\n");
    echo("            <input type=\"hidden\" name=\"action\" value=\"delete_bill_port\" />\n");
    echo("            <input type=\"hidden\" name=\"port_id\" value=\"".$port['port_id']."\" />\n");
//    echo("          </form>\n");
    echo("          <div class=\"btn-toolbar\">\n");
//    echo("            <div class=\"btn-group\">\n");
//    echo("              <a class=\"btn btn-danger\" href=\"javascript:;\" onclick=\"document.delete".$port['port_id'].".submit();\" style=\"color: #fff;\"><i class=\"icon-minus-sign icon-white\"></i> <strong>Remove Interface</strong></a>\n");
//    echo("              <a class=\"btn btn-danger\" href=\"".$delete."\" style=\"color: #fff;\"><i class=\"icon-minus-sign icon-white\"></i> <strong>Remove Interface</strong></a>\n");
//    echo("              <button type=\"submit\" class=\"btn btn-mini btn-danger\" style=\"color: #fff;\"><i class=\"icon-minus-sign icon-white\"></i> <strong>Remove Interface</strong></button>\n");
//    echo("            </div>\n");
    echo("            <div class=\"btn-group\">\n");
    //echo("            <div class=\"btn-group\" style=\"width: 600px;\">\n");
#    echo('              <a class="btn btn-danger" href="'.$delete.'" style="color: #fff;"><i class="icon-trash icon-white"></i> Delete</a>'.PHP_EOL);
    echo("              <button type=\"submit\" class=\"btn btn-danger\" style=\"color: #fff;\"><i class=\"icon-minus-sign icon-white\"></i> Delete</button>\n");
    echo("              ".$devicebtn."\n");
    echo("              ".$portbtn."\n");
    echo("            </div>\n");
    //echo("            <div class=\"btn-group\">\n");
    //echo("              <a class=\"btn btn-danger btn-mini\" href=\"javascript:;\" onclick=\"document.delete".$port['port_id'].".submit();\" style=\"color: #fff;\"><i class=\"icon-minus-sign icon-white\"></i> <strong>Remove Interface</strong></a>\n");
    //echo("            </div>\n");
    echo("          </div>\n");
    echo("          </form>\n");
  }
  if (!$emptyCheck) {
    echo("          <div class=\"alert alert-info\">\n");
    echo("            <i class=\"icon-info-sign\"></i> <strong>There are no ports assigned to this bill</strong>\n");
    echo("          </div>\n");
  }
}

?>
        </div>
      </fieldset>
    </form>
    <form action="<?php echo($url); ?>" method="post" class="form-horizontal">
      <input type="hidden" name="action" value="add_bill_port" />
      <input type="hidden" name="bill_id" value="<?php echo $bill_id; ?>" />
      <fieldset>
        <legend>Add New Port</legend>
        <div class="control-group">
          <label class="control-label" for="device"><strong>Device</strong></label>
          <div class="controls">
            <select style="width: 300px;" id="device" name="device" onchange="getInterfaceList(this)">
              <option value=''>Select a device</option>
<?php

$devices = dbFetchRows("SELECT * FROM `devices` ORDER BY hostname");
foreach ($devices as $device)
{
  unset($done);
  foreach ($access_list as $ac) { if ($ac == $device['device_id']) { $done = 1; } }
  if (!$done) { echo("              <option value='" . $device['device_id']  . "'>" . $device['hostname'] . "</option>\n"); }
}

?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="port_id"><strong>Interface</strong></label>
          <div class="controls">
            <select style="width: 300px;" id="port_id" name="port_id"></select>
          </div>
        </div>
      </fieldset>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary" name="Submit" value=" Add " /><i class="icon-plus-sign icon-white"></i> <strong>Add Interface</strong></button>
      </div>
    </form>
  </div>
</div>
