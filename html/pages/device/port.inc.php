<?php

if (!isset($vars['view']) ) { $vars['view'] = "graphs"; }

$sql  = "SELECT *, `ports`.`port_id` as `port_id`";
$sql .= " FROM  `ports`";
$sql .= " LEFT JOIN  `ports-state` ON  `ports`.port_id =  `ports-state`.port_id";
$sql .= " WHERE `ports`.`port_id` = ?";

$port = dbFetchRow($sql, array($vars['port']));

$port_details = 1;

$hostname = $device['hostname'];
$hostid   = $device['port_id'];
$ifname   = $port['ifDescr'];
$ifIndex   = $port['ifIndex'];
$speed = humanspeed($port['ifSpeed']);

$ifalias = $port['name'];

if ($port['ifPhysAddress']) { $mac = "$port[ifPhysAddress]"; }

$color = "black";
if ($port['ifAdminStatus'] == "down") { $status = "<span class='grey'>Disabled</span>"; }
if ($port['ifAdminStatus'] == "up" && $port['ifOperStatus'] == "down") { $status = "<span class='red'>Enabled / Disconnected</span>"; }
if ($port['ifAdminStatus'] == "up" && $port['ifOperStatus'] == "up") { $status = "<span class='green'>Enabled / Connected</span>"; }

$i = 1;
$inf = fixifName($ifname);
$show_all = 1;

echo("<table width=100% cellpadding=10 cellspacing=0>");
include("includes/print-interface.inc.php");
echo("</table>");

if ( strpos(strtolower($ifname), "vlan") !== false ) {  $broke = yes; }
if ( strpos(strtolower($ifname), "loopback") !== false ) {  $broke = yes; }

// Start Navbar

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'port',
                    'port'    => $port['port_id']);

$navbars['main']['options']['graphs']['text']   = 'Graphs';

if (dbFetchCell("SELECT COUNT(*) FROM `sensors` WHERE `measured_class` = 'port' AND `measured_entity` = '".$port['port_id']."' and `device_id` = '".$device['device_id']."'"))
{  $navbars['main']['options']['sensors']['text'] = 'Sensors'; }

$navbars['main']['options']['realtime']['text'] = 'Real time';   // FIXME CONDITIONAL
$navbars['main']['options']['arp']['text']      = 'ARP Table';   // FIXME CONDITIONAL?

if(dbFetchCell("SELECT COUNT(*) FROM `vlans_fdb` WHERE `port_id` = ?", array($port['port_id'])) ){
  $navbars['main']['options']['fdb']['text'] = 'FDB Table';
}

$navbars['main']['options']['events']['text']      = 'Eventlog';

if (dbFetchCell("SELECT COUNT(*) FROM `ports_adsl` WHERE `port_id` = ?", array($port['port_id'])) )
{  $navbars['main']['options']['adsl']['text'] = 'ADSL'; }

if (dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE `pagpGroupIfIndex` = '".$port['ifIndex']."' and `device_id` = '".$device['device_id']."'") )
{  $navbars['main']['options']['pagp']['text'] = 'PAgP'; }

if (dbFetchCell("SELECT COUNT(*) FROM `ports_vlans` WHERE `port_id` = '".$port['port_id']."' and `device_id` = '".$device['device_id']."'"))
{  $navbars['main']['options']['vlans']['text'] = 'VLANs'; }



if (dbFetchCell("SELECT count(*) FROM mac_accounting WHERE port_id = '".$port['port_id']."'") > "0" )
{

  $navbars['main']['options']['macacc']['text'] = 'MAC Accounting';

  echo(generate_link($descr,$link_array,array('view'=>'macacc','graph'=>$type)));
  $graphs = array('bits', 'packets');
  $option = 'macacc';


  $navbars['macacc']['class'] = "navbar-narrow";
  $navbars['macacc']['brand'] = $navbars['main']['options'][$option]['text'];

  foreach($graphs as $type)
  {
    if($vars['view'] == $option && $vars['graph'] == $type) { $navbars['main']['options'][$option]['suboptions'][$type]['class'] = "active"; }
    $navbars['main']['options'][$option]['suboptions'][$type]['text'] = ucfirst($type);
    $navbars['main']['options'][$option]['suboptions'][$type]['url']  = generate_url($vars,array('view' => 'macacc', 'subview' => 'graphs', 'graph'=>$type));

    $navbars['macacc']['options'][$type]['text'] = ucfirst($type);
    $navbars['macacc']['options'][$type]['url']  = generate_url($link_array,array('graph'=>$type));
    if($vars['graph'] == $type) { $navbars['macacc']['options'][$type]['class'] = "active"; }
  }

  $subviews = array('graphs', 'mini-graphs', 'top10');
  foreach($subviews as $type)
  {
    if($vars['view'] == $option && $vars['subview'] == $type) { $navbars['main']['options_right'][$option]['suboptions'][$type]['class'] = "active"; }
    $navbars['main']['options'][$option]['suboptions'][$type]['text'] = ucfirst($type);
    $navbars['main']['options'][$option]['suboptions'][$type]['url']  = generate_url($vars,array('subview'=>$type));

    $navbars['macacc']['options_right'][$type]['text'] = ucfirst($type);
    $navbars['macacc']['options_right'][$type]['url']  = generate_url($link_array,array('subview'=>$type));
    if($vars['graph'] == $type) { $navbars['macacc']['options'][$type]['class'] = "active"; }
  }
}

if (dbFetchCell("SELECT COUNT(*) FROM juniAtmVp WHERE port_id = '".$port['port_id']."'") > "0" )
{

  // FIXME ATM VPs
  // FIXME URLs BROKEN

  $navbars['main']['options']['atm-vp']['text'] = 'ATM VPs';

  $graphs = array('bits', 'packets', 'cells', 'errors');
  foreach($graphs as $type)
  {
    if($vars['view'] == "atm-vp" && $vars['graph'] == $type) { $navbars['main']['options']['atm-vp']['suboptions'][$type]['class'] = "active"; }
    $navbars['main']['options']['atm-vp']['suboptions'][$type]['text'] = ucfirst($type);
    $navbars['main']['options']['atm-vp']['suboptions'][$type]['url']  = generate_url($link_array,array('view'=>'atm-vc','graph'=>$type));

  }
}

if ($_SESSION['userlevel'] == '10' && $config['enable_billing'])
{
  $navbars['main']['options_right']['bills'] = array('text' => 'Create Bill', 'icon' => 'fugue-money-coin', 'url' => generate_url(array('page' => 'bills', 'view' => 'add', 'port' => $port['port_id'])));
}


foreach ($navbars['main']['options'] as $option => $array)
{
  if ($vars['view'] == $option) { $navbars['main']['options'][$option]['class'] .= " active"; }
  $navbars['main']['options'][$option]['url'] = generate_url($link_array,array('view'=>$option));
}

$navbars['main']['class'] = "navbar-narrow";
$navbars['main']['brand'] = "Port";

foreach($navbars as $type => $navbar)
{
  if($type == $vars['view'] || $type == 'main')
    print_navbar($navbar);
}

unset($navbars);

include("pages/device/port/".mres($vars['view']).".inc.php");

?>
