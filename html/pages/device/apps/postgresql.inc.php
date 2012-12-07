<?php

global $config;

$app_sections = array('stats' => "Stats",
		      'live' => "Live");

print_optionbar_start();
echo('<span style="font-weight: bold;">'.$app["app_type"].'</span> &#187; ');
unset($sep);
foreach ($app_sections as $app_section => $app_section_text)
{
    echo($sep);
    if (!$vars['app_section']) { $vars['app_section'] = $app_section; }
    if ($vars['app_section'] == $app_section)
    {
	echo("<span class='pagemenu-selected'>");
    }
    echo(generate_link(ucfirst($app_section),$vars,array('app_section'=>$app_section)));
    if ($vars['app_section'] == $app_section) { echo("</span>"); }
    $sep = " | ";
}
print_optionbar_end();

$graphs['stats'] = array('postgresql_xact'  => 'Postgresql Commit Count',
			 'postgresql_blks' => 'Postgresql Blocks Count',
			 'postgresql_tuples' => 'Postgresql Tuples Count',
			 'postgresql_tuples_query' => 'Postgresql Tuples Count per Query');

$graphs['live'] = array('postgresql_connects' => 'Postgresql Connection Count',
			'postgresql_queries' => 'Postgresql Query Types');


foreach ($graphs[$vars['app_section']] as $key => $text) {
    $graph_type            = $key;
    $graph_array['height'] = "100";
    $graph_array['width']  = "215";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $app['app_id'];
    $graph_array['type']   = "application_".$key;
    echo("<h3>".$text."</h3>");
    echo("<tr bgcolor='$row_colour'><td colspan=5>");
    
    include("includes/print-graphrow.inc.php");
    
    echo("</td></tr>");
}

?>