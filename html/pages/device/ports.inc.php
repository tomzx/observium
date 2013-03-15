<?php

if ($vars['view'] == 'graphs' || $vars['view'] == 'minigraphs')
{
  if (isset($vars['graph'])) { $graph_type = "port_" . $vars['graph']; } else { $graph_type = "port_bits"; }
}

if (!$vars['view']) { $vars['view'] = trim($config['ports_page_default'],'/'); }

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'ports');

$navbar['options']['basic']['text']   = 'Basic';
$navbar['options']['details']['text'] = 'Details';
$navbar['options']['arp']['text']     = 'ARP/NDP Table';

if(dbFetchCell("SELECT COUNT(*) FROM `vlans_fdb` WHERE `device_id` = ?", array($device['device_id'])))
{
  $navbar['options']['fdb']['text'] = 'FDB Table';
}

if(dbFetchCell("SELECT * FROM links AS L, ports AS I WHERE I.device_id = ? AND I.port_id = L.local_port_id", array($device['device_id'])))
{
  $navbar['options']['neighbours']['text'] = 'Neighbours';
  $navbar['options']['map']['text']        = 'Map';
}
if(dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE `ifType` = 'adsl' AND `device_id` = ?", array($device['device_id'])))
{
  $navbar['options']['adsl']['text'] = 'ADSL';
}

  $navbar['options']['graphs']     = array('text' => 'Graphs', 'class' => 'pull-right');
  $navbar['options']['minigraphs'] = array('text' => 'Minigraphs', 'class' => 'pull-right');


foreach ($navbar['options'] as $option => $array)
{
  if ($vars['view'] == $option) { $navbar['options'][$option]['class'] .= " active"; }
  $navbar['options'][$option]['url'] = generate_url($link_array,array('view'=>$option));
}


  $graph_types = array("bits" => "Bits",
                       "upkts" => "Ucast Packets",
                       "nupkts" => "NUcast Packets",
                       "errors" => "Errors",
                       "etherlike" => "Etherlike");

foreach (array('graphs', 'minigraphs') as $option)
{
 foreach ($graph_types as $type => $descr)
 {

  if($vars['view'] == $option)
  {
    $navbar_b['class'] = "navbar-narrow";
    $navbar_b['brand'] = $navbar['options'][$option]['text'];

    $navbar_b['options'][$type]['text'] = $descr;
    $navbar_b['options'][$type]['url']  = generate_url($link_array,array('view'=>'graphs','graph'=>$type));
    if($vars['graph'] == $type) { $navbar_b['options'][$type]['class'] = "active"; }

  }
  if($vars['view'] == $option && $vars['graph'] == $type) { $navbar['options'][$option]['suboptions'][$type]['class'] = "active"; }
  $navbar['options'][$option]['suboptions'][$type]['text'] = $descr;
  $navbar['options'][$option]['suboptions'][$type]['url']  = generate_url($link_array,array('view'=>$option,'graph'=>$type));
 }
}

$navbar['class'] = "navbar-narrow";
$navbar['brand'] = "Ports";
print_navbar($navbar);
unset($navbar);

if(is_array($navbar_b)) { print_navbar($navbar_b); }

if ($vars['view'] == 'minigraphs')
{
  $timeperiods = array('-1day','-1week','-1month','-1year');
  $from = '-1day';
  echo("<div style='display: block; clear: both; margin: auto; min-height: 500px;'>");
  unset ($seperator);

  // FIXME - FIX THIS. UGLY.
  foreach (dbFetchRows("select * from ports WHERE device_id = ? ORDER BY ifIndex", array($device['device_id'])) as $port)
  {
    echo("<div style='display: block; padding: 3px; margin: 3px; min-width: 183px; max-width:183px; min-height:90px; max-height:90px; text-align: center; float: left; background-color: #e9e9e9;'>
    <div style='font-weight: bold;'>".makeshortif($port['ifDescr'])."</div>
    <a href=\"" . generate_port_url($port) . "\" onmouseover=\"return overlib('\
    <div style=\'font-size: 16px; padding:5px; font-weight: bold; color: #e5e5e5;\'>".$device['hostname']." - ".$port['ifDescr']."</div>\
    ".$port['ifAlias']." \
    <img src=\'graph.php?type=".$graph_type."&amp;id=".$port['port_id']."&amp;from=".$from."&amp;to=".$config['time']['now']."&amp;width=450&amp;height=150\'>\
    ', CENTER, LEFT, FGCOLOR, '#e5e5e5', BGCOLOR, '#e5e5e5', WIDTH, 400, HEIGHT, 150);\" onmouseout=\"return nd();\"  >".
    "<img src='graph.php?type=".$graph_type."&amp;id=".$port['port_id']."&amp;from=".$from."&amp;to=".$config['time']['now']."&amp;width=180&amp;height=45&amp;legend=no'>
    </a>
    <div style='font-size: 9px;'>".truncate(short_port_descr($port['ifAlias']), 32, '')."</div>
    </div>");
  }
  echo("</div>");
} elseif ($vars['view'] == "arp" || $vars['view'] == "adsl" || $vars['view'] == "neighbours" || $vars['view'] == "fdb" || $vars['view'] == "map") {
  include("ports/".$vars['view'].".inc.php");
} else {
  if ($vars['view'] == "details") { $port_details = 1; }

if($vars['view'] == "graphs") { $table_class = "table-striped-two"; } else { $table_class = "table-striped"; }
echo('<table class="table table-hover table-condensed '.$table_class.'">');
echo('  <thead>');

echo('<tr>');

$cols = array(
              'state' => NULL,
              'BLANK' => NULL,
              'port' => 'Port',
              'graphs' => NULL,
              'traffic' => 'Traffic',
              'speed' => 'Speed',
              'media' => 'Media',
              'mac' => 'MAC Address',
              'details' => NULL);

foreach ($cols as $sort => $col)
{
  if ($col == NULL)
  {
    echo('<th></th>');
  }
  elseif ($vars['sort'] == $sort)
  {
    echo('<th>'.$col.' *</th>');
  } else {
    echo('<th><a href="'. generate_url($vars, array('sort' => $sort)).'">'.$col.'</a></th>');
  }
}

echo("      </tr>");

echo('  </thead>');

  $i = "1";

  global $port_cache, $port_index_cache;

  $sql  = "SELECT *, `ports`.`port_id` as `port_id`";
  $sql .= " FROM  `ports`";
  $sql .= " LEFT JOIN `ports-state` ON  `ports`.`port_id` =  `ports-state`.`port_id`";
  $sql .= " WHERE `device_id` = ? ORDER BY `ifIndex` ASC";
  $ports = dbFetchRows($sql, array($device['device_id']));

  // Sort ports, sharing code with global ports page.
  include("includes/port-sort.inc.php");

  // As we've dragged the whole database, lets pre-populate our caches :)
  // FIXME - we should probably split the fetching of link/stack/etc into functions and cache them here too to cut down on single row queries.
  foreach ($ports as $port)
  {
    $port_cache[$port['port_id']] = $port;
    $port_index_cache[$port['device_id']][$port['ifIndex']] = $port;
  }

  foreach ($ports as $port)
  {
    include("includes/print-interface.inc.php");
    $i++;
  }
  echo("</table></div>");
}

$pagetitle[] = "Ports";

?>
