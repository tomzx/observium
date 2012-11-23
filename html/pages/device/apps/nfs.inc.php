<?php

global $config;

$graphs = array('nfs_nfs2' => 'NFS2',
                'nfs_nfs3' => 'NFS3',
                'nfs_nfs4'  => 'NFS4');

foreach ($graphs as $key => $text)
{
  $graph_type = "nfs_scoreboard";

  $graph_array['height'] = "100";
  $graph_array['width']  = "215";
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;

  echo('<h3>'.$text.'</h3>');

  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
