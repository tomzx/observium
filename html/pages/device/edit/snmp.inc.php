<?php

if ($_POST['editing'])
{
  if ($_SESSION['userlevel'] > "7")
  {
    $community = mres($_POST['community']);
    $snmpver = mres($_POST['snmpver']);
    $port = mres($_POST['port']);
    $timeout = mres($_POST['timeout']);
    $retries = mres($_POST['retries']);
    $v3 = array (
      'authlevel' => mres($_POST['authlevel']),
      'authname' => mres($_POST['authname']),
      'authpass' => mres($_POST['authpass']),
      'authalgo' => mres($_POST['authalgo']),
      'cryptopass' => mres($_POST['cryptopass']),
      'cryptoalgo' => mres($_POST['cryptoalgo'])
    );

    #FIXME needs better feedback
    $update = array(
      'community' => $community,
      'snmpver' => $snmpver,
      'port' => $port
    );

    if ($_POST['timeout']) { $update['timeout'] = $timeout; }
      else { $update['timeout'] = array('NULL'); }
    if ($_POST['retries']) { $update['retries'] = $retries; }
      else { $update['retries'] = array('NULL'); }

    $update = array_merge($update, $v3);

    $rows_updated = dbUpdate($update, 'devices', '`device_id` = ?',array($device['device_id']));

    if ($rows_updated > 0)
    {
      $update_message = $rows_updated . " Device record updated.";
      $updated = 1;
    } elseif ($rows_updated = '-1') {
      $update_message = "Device record unchanged. No update necessary.";
      $updated = -1;
    } else {
      $update_message = "Device record update error.";
      $updated = 0;
    }
  }
}

$device = dbFetchRow("SELECT * FROM `devices` WHERE `device_id` = ?", array($device['device_id']));
$descr  = $device['purpose'];

if ($updated && $update_message)
{
  print_message($update_message);
} elseif ($update_message) {
  print_error($update_message);
}

?>

<form id="edit" name="edit" method="post" class="form-horizontal" action="">
  <input type=hidden name="editing" value="yes">
  <fieldset>
    <legend>SNMP Properties</legend>
    <div class="control-group">
      <label class="control-label" for="snmpver">SNMP Version</label>
      <div class="controls">
        <select name="snmpver">
          <option value="v1"  <?php echo($device['snmpver'] == 'v1' ? 'selected' : ''); ?> >v1</option>
          <option value="v2c" <?php echo($device['snmpver'] == 'v2c' ? 'selected' : ''); ?> >v2c</option>
          <option value="v3"  <?php echo($device['snmpver'] == 'v3' ? 'selected' : ''); ?> >v3</option>
        </select>
      </div>
    </div>
  </fieldset>

  <!-- To be able to hide it -->
  <div id="snmpv12">
    <fieldset>
      <legend>SNMPv1/v2c Configuration</legend>
      <div class="control-group">
        <label class="control-label" for="community">SNMP Community</label>
        <div class="controls">
          <input type=text name="community" size="32" value="<?php echo $device['community']; ?>"/>
        </div>
      </div>
    </fieldset>
  </div>

  <!-- To be able to hide it -->
  <div id='snmpv3'>
    <fieldset>
      <legend>SNMPv3 Configuration</legend>
      <div class="control-group">
        <label class="control-label" for="authlevel">Auth Level</label>
        <div class="controls">
          <select name="authlevel">
            <option value="NoAuthNoPriv" <?php echo($device['authlevel'] == 'NoAuthNoPriv' ? 'selected' : ''); ?> >NoAuthNoPriv</option>
            <option value="AuthNoPriv"   <?php echo($device['authlevel'] == 'authNoPriv' ? 'selected' : ''); ?> >authNoPriv</option>
            <option value="AuthPriv"     <?php echo($device['authlevel'] == 'authPriv' ? 'selected' : ''); ?> >authPriv</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="authname">Auth User Name</label>
        <div class="controls">
          <input type=text name="authname" size="32" value="<?php echo $device['authname']; ?>"/>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="authpass">Auth Password</label>
        <div class="controls">
          <input type="password" name="authpass" size="32" value="<?php echo $device['authpass']; ?>"/>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="authalgo">Auth Algorithm</label>
        <div class="controls">
          <select name="authalgo">
            <option value="MD5">MD5</option>
            <option value="SHA" <?php echo($device['authalgo'] == 'SHA' ? 'selected' : ''); ?> >SHA</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="cryptopass">Crypto Password</label>
        <div class="controls">
          <input type="password" name="cryptopass" size="32" value="<?php echo $device['cryptopass']; ?>"/>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="cryptoalgo">Crypto Algorithm</label>
        <div class="controls">
          <select name="authalgo">
            <option value="AES">AES</option>
            <option value="DES" <?php echo($device['cryptoalgo'] == "DES" ? 'selected' : ''); ?> >DES</option>
          </select>
        </div>
      </div>

    </fieldset>
  </div>

    <fieldset>
      <legend>SNMP Connectivity</legend>

      <div class="control-group">
        <label class="control-label" for="transport">SNMP Transport</label>
        <div class="controls">
          <select name="transport">
<?php
foreach ($config['snmp']['transports'] as $transport)
{
  echo("<option value='".$transport."'");
  if ($transport == $device['transport']) { echo(" selected='selected'"); }
  echo(">".$transport."</option>");
}
?>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="port">SNMP Port</label>
        <div class="controls">
          <input type=text name="port" size="32" value="<?php echo $device['port']; ?>"/>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="timeout">SNMP Timeout</label>
        <div class="controls">
          <input type=text name="timeout" size="32" value="<?php echo $device['timeout']; ?>"/>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="retries">SNMP Retries</label>
        <div class="controls">
          <input type=text name="retries" size="32" value="<?php echo $device['retries']; ?>"/>
        </div>
      </div>
    </fieldset>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary" name="submit" value="save"><i class="icon-ok icon-white"></i> Save Changes</button>
  </div>

</form>
