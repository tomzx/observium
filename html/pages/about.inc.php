<h2>About Observium</h2>
<div class="row">
  <div class="col-md-6">
<?php

$observium_version = $config['version'];
$apache_version = str_replace("Apache/", "", $_SERVER['SERVER_SOFTWARE']);
$php_version = phpversion();
$mysql_version = dbFetchCell("SELECT version()");
//$netsnmp_version = shell_exec($config['snmpget'] . " --version 2>&1");
$rrdtool_version = implode(" ",array_slice(explode(" ",shell_exec($config['rrdtool'] . " --version |head -n1")),1,1));

?>
  <div class="well info_box">
    <div class="title"><i class="oicon-information"></i> Version Information</div>
    <div class="content">
        <table class="table table-striped table-condensed-more">
          <tbody>
            <tr><td><b>Observium</b></td><td><?php echo($observium_version); ?></td></tr>
            <tr><td><b>Apache</b></td><td><?php echo($apache_version); ?></td></tr>
            <tr><td><b>PHP</b></td><td><?php echo($php_version); ?></td></tr>
            <tr><td><b>MySQL</b></td><td><?php echo($mysql_version); ?></td></tr>
            <tr><td><b>RRDtool</b></td><td><?php echo($rrdtool_version); ?></td></tr>
          </tbody>
        </table>
    </div>
  </div>

  <div style="margin-bottom: 20px; margin-top: 10px;">
  <table width=100%>
    <tr>
      <td width="20%" align=center><a class="btn btn-small" href="http://www.observium.org"><i style="font-size: small;" class="icon-globe"></i> Web</a></td>
      <td width="20%" align=center><a class="btn btn-small" href="http://jira.observium.org/"><i style="font-size: small;" class="icon-bug"></i> Bugtracker</a></td>
      <td width="20%" align=center><a class="btn btn-small" href="http://www.observium.org/wiki/Mailing_Lists"><i style="font-size: small;" class="icon-envelope"></i> Mailing List</a></td>
      <td width="20%" align=center><a class="btn btn-small" href="http://twitter.com/observium"><i style="font-size: small;" class="icon-twitter-sign"></i> Twitter</a></td>
      <!--<td><a class="btn btn-small" href="http://twitter.com/observium_svn"><i class="icon-twitter-sign"></i> SVN Twitter</a></td>-->
      <td width="20%" align=center><a class="btn btn-small" href="http://www.facebook.com/pages/Observium/128354461353"><i style="font-size: small;" class="icon-facebook-sign"></i> Facebook</a></td>
    </tr>
  </table>
  </div>

  <div class="well info_box">
    <div class="title"><i class="oicon-user-detective"></i> Development Team</div>
    <div class="content">
        <dl class="dl-horizontal" style="margin: 0px 0px 5px 0px;">
          <dt style="text-align: left;"><i class="icon-user"></i> Adam Armstrong</dt><dd>Project Leader</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Tom Laermans</dt><dd>Committer & Developer</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Geert Hauwaerts</dt><dd>Developer</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Dennis de Houx</dt><dd>Developer</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Mike Stupalov</dt><dd>Developer</dd>
        </dl>
    </div>
  </div>

  <div class="well info_box">
    <div class="title"><i class="oicon-users"></i> Acknowledgements</div>
    <div class="content">
        <dl class="dl-horizontal" style="margin: 0px 0px 5px 0px;">
          <dt style="text-align: left;"><i class="icon-user"></i> Twitter</dt><dd>Bootstrap CSS Framework</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> <a href="mailto:p@yusukekamiyamane.com" alt="p@yusukekamiyamane.com">Yusuke Kamiyamane</a></dt><dd>Fugue Iconset</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Stu Nicholls</dt><dd>Dropdown menu CSS code.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Mark James</dt><dd>Silk Iconset.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Erik Bosrup</dt><dd>Overlib Library.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Jonathan De Graeve</dt><dd>SNMP code improvements.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Xiaochi Jin</dt><dd>Logo design.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Akichi Ren</dt><dd>Post-steampunk observational hamster</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Bruno Pramont</dt><dd>Collectd code.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> <a href="mailto:DavidPFarrell@gmail.com" alt="DavidPFarrell@gmail.com">David Farrell</a></dt><dd>Help with parsing net-SNMP output in PHP.</dd>
          <dt style="text-align: left;"><i class="icon-user"></i> Job Snijders</dt><dd>Python-based multi-instance poller wrapper.</dd>
        </dl>
        </div>
      </div>

  <div class="well info_box">
    <div class="title"><i class="oicon-chart"></i> Statistics</div>
    <div class="content">

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
            <td width=45%><i class='oicon-servers'></i> <strong>Devices</strong></td><td><span class='pull-right'>$stat_devices</span></td>
            <td width=45%><i class='oicon-network-ethernet'></i> <strong>Ports</strong></td><td><span class='pull-right'>$stat_ports</span></td>
          </tr>
          <tr>
            <td><i class='oicon-ipv4'></i> <b>IPv4 Addresses</strong></td><td><span class='pull-right'>$stat_ipv4_addy</span></td>
            <td><i class='oicon-ipv4'></i> <b>IPv4 Networks</strong></td><td><span class='pull-right'>$stat_ipv4_nets</span></td>
          </tr>
          <tr>
            <td><i class='oicon-ipv6'></i> <strong>IPv6 Addresses</strong></td><td><span class='pull-right'>$stat_ipv6_addy</span></td>
            <td><i class='oicon-ipv6'></i> <strong>IPv6 Networks</strong></td><td><span class='pull-right'>$stat_ipv6_nets</span></td>
           </tr>
         <tr>
            <td><i class='oicon-gear'></i> <strong>Services</strong></td><td><span class='pull-right'>$stat_services</span></td>
            <td><i class='oicon-application-icon-large'></i> <strong>Applications</strong></td><td><span class='pull-right'>$stat_apps</span></td>
          </tr>
          <tr>
            <td><i class='oicon-processor'></i> <strong>Processors</strong></td><td><span class='pull-right'>$stat_processors</span></td>
            <td><i class='oicon-memory'></i> <strong>Memory</strong></td><td><span class='pull-right'>$stat_memory</span></td>
          </tr>
          <tr>
            <td><i class='oicon-drive'></i> <strong>Storage</strong></td><td><span class='pull-right'>$stat_storage</span></td>
            <td><i class='oicon-drive--arrow'></i> <strong>Disk I/O</strong></td><td><span class='pull-right'>$stat_diskio</span></td>
          </tr>
          <tr>
            <td><i class='oicon-wooden-box'></i> <strong>HR-MIB</strong></td><td><span class='pull-right'>$stat_hrdev</span></td>
            <td><i class='oicon-wooden-box'></i> <strong>Entity-MIB</strong></td><td><span class='pull-right'>$stat_entphys</span></td>
          </tr>
          <tr>
            <td><i class='oicon-clipboard-eye'></i> <strong>Syslog Entries</strong></td><td><span class='pull-right'>$stat_syslog</span></td>
            <td><i class='oicon-clipboard-audit'></i> <strong>Eventlog Entries</strong></td><td><span class='pull-right'>$stat_events</span></td>
          </tr>
          <tr>
            <td><i class='oicon-system-monitor'></i> <strong>Sensors</strong></td><td><span class='pull-right'>$stat_sensors</span></td>
            <td><i class='oicon-printer-color'></i> <strong>Toner</string></td><td><span class='pull-right'>$stat_toner</span></td>
          </tr>
        </tbody>
      </table>
");
?>
      </div>
    </div>
  </div>
  <div class="col-md-6">

      <div class="alert alert-info" style="margin-top: 15px; text-align: center;">
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

  <div class="well info_box">
    <div class="title"><i class="oicon-notebook"></i> License</div>
    <div class="content">
      <pre>
<?php include("../LICENSE"); ?>
      </pre>
    </div>
  </div>
  </div>
</div>
