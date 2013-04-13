<?php

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'latency');

$navbar['brand'] = "Latency";
$navbar['class'] = "navbar-narrow";

foreach (array('incoming', 'outgoing') as $view)
{
  if(!strlen($vars['view'])) { $vars['view'] = $view; }

  if ($vars['view'] == $view) { $navbar['options'][$view]['class'] = "active"; }
  $navbar['options'][$view]['url'] = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'latency', 'view' => $view));
  $navbar['options'][$view]['text'] = ucwords($view);
}

print_navbar($navbar);


echo('<table class="table table-striped">');

if($vars['view'] == "incoming")
{

    if (count($smokeping_files['in'][$device['hostname']]))
    {

       $graph_array['type']                    = "device_smokeping_in_all_avg";
       $graph_array['device']                      = $device['device_id'];
       echo('<tr><td>');
       echo('<h3>Average</h3>');

       include("includes/print-graphrow.inc.php");

       echo('</td></tr>');

       $graph_array['type']                    = "device_smokeping_in_all";
       $graph_array['legend']                  = no;
       echo('<tr><td>');
       echo('<h3>Aggregate</h3>');

       include("includes/print-graphrow.inc.php");

       echo('</td></tr>');

       unset($graph_array['legend']);

       ksort($smokeping_files['in'][$device['hostname']]);
       foreach ($smokeping_files['in'][$device['hostname']] AS $src => $host)
       {
         $hostname = str_replace(".rrd", "", $host);
         $host = device_by_name($src);
         if (is_numeric($host['device_id']))
         {
           echo('<tr><td>');
           echo('<h3>'.generate_device_link($host).'</h3>');
           $graph_array['type']                    = "smokeping_in";
           $graph_array['device']                      = $device['device_id'];
           $graph_array['src']                     = $host['device_id'];

           include("includes/print-graphrow.inc.php");

           echo('</td></tr>');
         }
       }

    }

} elseif ($vars['view'] == "outgoing") {

    if (count($smokeping_files['out'][$device['hostname']]))
    {

       $graph_array['type']                    = "device_smokeping_out_all_avg";
       $graph_array['device']                      = $device['device_id'];
       echo('<tr><td>');
       echo('<h3>Aggregate</h3>');

       include("includes/print-graphrow.inc.php");

       echo('</td></tr>');

       $graph_array['type']                    = "device_smokeping_out_all";
       $graph_array['legend']                  = no;
       echo('<tr><td>');
       echo('<h3>Aggregate</h3>');

       include("includes/print-graphrow.inc.php");

       echo('</td></tr>');

       unset($graph_array['legend']);

       asort($smokeping_files['out'][$device['hostname']]);
       foreach ($smokeping_files['out'][$device['hostname']] AS $host)
       {
         $hostname = str_replace(".rrd", "", $host);
         list($hostname) = explode("~", $hostname);
         $host = device_by_name($hostname);
         if (is_numeric($host['device_id']))
         {
           echo('<tr><td>');
           echo('<h3>'.generate_device_link($host).'</h3>');
           $graph_array['type']                    = "smokeping_out";
           $graph_array['device']                      = $device['device_id'];
           $graph_array['dest']                         = $host['device_id'];

           include("includes/print-graphrow.inc.php");

           echo('</td></tr>');
         }
       }

    }
}

echo('</table>');

$pagetitle[] = "Latency";

?>
