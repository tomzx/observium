<h2>About Observium</h2>
<div class="row-fluid">
  <div class="span6">
<?php

$Observium_version = $config['version'];

echo("
      <div class='well well-small'>
        <h3>Versions</h3>
        <table class=\"table table-bordered table-striped table-condensed table-rounded\">
          <tbody>
            <tr><td><b>Observium</b></td><td>$Observium_version</td></tr>
            <tr><td><b>Apache</b></td><td>$apache_version</td></tr>
            <tr><td><b>PHP</b></td><td>$php_version</td></tr>
            <tr><td><b>MySQL</b></td><td>$mysql_version</td></tr>
            <tr><td><b>RRDtool</b></td><td>$rrdtool_version</td></tr>
          </tbody>
        </table>
");

?>

        <p>
          <a class="btn btn-mini" href="http://www.observium.org"><i class="icon icon-globe"></i> Website</a>
          <a class="btn btn-mini" href="http://jira.observium.org/"><i class="icon icon-fire"></i> Bugtracker</a>
          <a class="btn btn-mini" href="http://www.observium.org/wiki/Mailing_Lists"><i class="icon icon-envelope"></i> Mailing List</a>
          <a class="btn btn-mini" href="http://twitter.com/observium"><i class="icon icon-share-alt"></i> Twitter</a>
          <a class="btn btn-mini" href="http://twitter.com/observium_svn"><i class="icon icon-share-alt"></i> SVN Twitter</a>
          <a class="btn btn-mini" href="http://www.facebook.com/pages/Observium/128354461353"><i class="icon icon-thumbs-up"></i> Facebook</a>
        </p>

        <h3>The Team</h3>
        <div class="well well-light">
        <dl class="dl-horizontal" style="margin: 0px 0px 5px 0px;">
          <dt style="text-align: left;"><img src="images/icons/flags/gb.png"> Adam Armstrong</dt><dd>Project Founder</dd>
          <dt style="text-align: left;"><img src="images/icons/flags/be.png"> Tom Laermans</dt><dd>Developer/Committer</dd>
          <dt style="text-align: left;"><img src="images/icons/flags/be.png"> Geert Hauwaerts</dt><dd>Developer</dd>
          <dt style="text-align: left;"><img src="images/icons/flags/be.png"> Dennis de Houx</dt><dd>Developer</dd>
        </dl>
        </div>

        <h3>Acknowledgements</h3>
        <div class="well well-light">
        <dl class="dl-horizontal" style="margin: 0px 0px 5px 0px;">
          <dt style="text-align: left;"><i class="icon-user"></i> Stu Nicholls</dt><dd>Dropdown menu CSS code.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Mark James</dt><dd>Silk Iconset.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Erik Bosrup</dt><dd>Overlib Library.</dd>
          <dt style="text-align: left; width: 180px;"><i class="icon-user"></i> Jonathan De Graeve</dt><dd>SNMP code improvements.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Xiaochi Jin</dt><dd>Logo design.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Bruno Pramont</dt><dd>Collectd code.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> David Farrell <DavidPFarrell@gmail.com></dt><dd>Help with parsing net-SNMP output in PHP.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Job Snijders</dt><dd>Python-based multi-instance poller wrapper script.</dd>
        </dl>
        </div>
      </div>

      <div class="well">
        <h3>Statistics</h3>

<?php
$stat_devices = dbFetchCell("SELECT COUNT(device_id) FROM `devices`");
$stat_ports = dbFetchCell("SELECT COUNT(port_id) FROM `ports`");
$stat_syslog = dbFetchCell("SELECT COUNT(seq) FROM `syslog`");
$stat_events = dbFetchCell("SELECT COUNT(event_id) FROM `eventlog`");
$stat_apps = dbFetchCell("SELECT COUNT(app_id) FROM `applications`");
$stat_services = dbFetchCell("SELECT COUNT(service_id) FROM `services`");
$stat_storage = dbFetchCell("SELECT COUNT(storage_id) FROM `storage`");
$stat_diskio = dbFetchCell("SELECT COUNT(diskio_id) FROM `ucd_diskio`");
$stat_processors = dbFetchCell("SELECT COUNT(processor_id) FROM `processors`");
$stat_memory = dbFetchCell("SELECT COUNT(mempool_id) FROM `mempools`");
$stat_sensors = dbFetchCell("SELECT COUNT(sensor_id) FROM `sensors`");
$stat_toner = dbFetchCell("SELECT COUNT(toner_id) FROM `toner`");
$stat_hrdev = dbFetchCell("SELECT COUNT(hrDevice_id) FROM `hrDevice`");
$stat_entphys = dbFetchCell("SELECT COUNT(entPhysical_id) FROM `entPhysical`");

$stat_ipv4_addy = dbFetchCell("SELECT COUNT(ipv4_address_id) FROM `ipv4_addresses`");
$stat_ipv4_nets = dbFetchCell("SELECT COUNT(ipv4_network_id) FROM `ipv4_networks`");
$stat_ipv6_addy = dbFetchCell("SELECT COUNT(ipv6_address_id) FROM `ipv6_addresses`");
$stat_ipv6_nets = dbFetchCell("SELECT COUNT(ipv6_network_id) FROM `ipv6_networks`");

$stat_pw = dbFetchCell("SELECT COUNT(pseudowire_id) FROM `pseudowires`");
$stat_vrf = dbFetchCell("SELECT COUNT(vrf_id) FROM `vrfs`");
$stat_vlans = dbFetchCell("SELECT COUNT(vlan_id) FROM `vlans`");

echo("
      <table class=\"table table-bordered table-striped table-condensed table-rounded\">
        <tbody>
          <tr>
            <td width=45%><img src='images/icons/device.png' class='optionicon'> <b>Devices</b></td><td align=right>$stat_devices</td>
            <td width=45%><img src='images/icons/port.png' class='optionicon'> <b>Ports</b></td><td align=right>$stat_ports</td>
          </tr>
          <tr>
            <td><img src='images/icons/ipv4.png'  class='optionicon'> <b>IPv4 Addresses<b></td><td align=right>$stat_ipv4_addy</td>
            <td><img src='images/icons/ipv4.png' class='optionicon'> <b>IPv4 Networks</b></td><td align=right>$stat_ipv4_nets</td>
          </tr>
          <tr>
            <td><img src='images/icons/ipv6.png'  class='optionicon'> <b>IPv6 Addresses<b></td><td align=right>$stat_ipv6_addy</td>
            <td><img src='images/icons/ipv6.png' class='optionicon'> <b>IPv6 Networks</b></td><td align=right>$stat_ipv6_nets</td>
           </tr>
         <tr>
            <td><img src='images/icons/services.png'  class='optionicon'> <b>Services<b></td><td align=right>$stat_services</td>
            <td><img src='images/icons/apps.png' class='optionicon'> <b>Applications</b></td><td align=right>$stat_apps</td>
          </tr>
          <tr>
            <td ><img src='images/icons/processor.png' class='optionicon'> <b>Processors</b></td><td align=right>$stat_processors</td>
            <td><img src='images/icons/memory.png' class='optionicon'> <b>Memory</b></td><td align=right>$stat_memory</td>
          </tr>
          <tr>
            <td><img src='images/icons/storage.png' class='optionicon'> <b>Storage</b></td><td align=right>$stat_storage</td>
            <td><img src='images/icons/diskio.png' class='optionicon'> <b>Disk I/O</b></td><td align=right>$stat_diskio</td>
          </tr>
          <tr>
            <td><img src='images/icons/inventory.png' class='optionicon'> <b>HR-MIB</b></td><td align=right>$stat_hrdev</td>
            <td><img src='images/icons/inventory.png' class='optionicon'> <b>Entity-MIB</b></td><td align=right>$stat_entphys</td>
          </tr>
          <tr>
            <td ><img src='images/icons/syslog.png' class='optionicon'> <b>Syslog Entries</b></td><td align=right>$stat_syslog</td>
            <td><img src='images/icons/eventlog.png' class='optionicon'> <b>Eventlog Entries</b></td><td align=right>$stat_events</td>
          </tr>
          <tr>
            <td ><img src='images/icons/sensors.png' class='optionicon'> <b>Sensors</b></td><td align=right>$stat_sensors</td>
            <td><img src='images/icons/toner.png' class='optionicon'> <b>Toner</b></td><td align=right>$stat_toner</td>
          </tr>
        </tbody>
      </table>
");
?>
    </div>
  </div>
  <div class="span6">

      <div class="alert alert-info" style="text-align: center;">
        <h3>Observium is a Free software project. <br />Please donate to support continued development.</h3>
        <div style="margin-top:10px;">
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="W2ZJ3JRZR72Z6">
            <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal . The safer, easier way to pay online.">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
          </form>
        </div>
      </div>

    <div class="well">
      <h3>License</h3>
      <pre>
<?php include("../LICENSE"); ?>
      </pre>
    </div>
  </div>
</div>
