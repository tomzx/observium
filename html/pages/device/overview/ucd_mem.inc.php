  <div class="well info_box">
    <div id="title"><i class="oicon-memory"></i> Memory Usage</div>
    <div id="content">

<?php

$mem_used = $device_state['ucd_mem']['mem_total'] - ($device_state['ucd_mem']['mem_avail'] + $device_state['ucd_mem']['mem_shared'] + $device_state['ucd_mem']['mem_cached'] + $device_state['ucd_mem']['mem_buffer']);

$used_perc = round(($mem_used / $device_state['ucd_mem']['mem_total']) * 100);
$cach_perc = round(($device_state['ucd_mem']['mem_cached'] / $device_state['ucd_mem']['mem_total']) * 100);
$buff_perc = round(($device_state['ucd_mem']['mem_buffer'] / $device_state['ucd_mem']['mem_total']) * 100);
$shar_perc = round(($device_state['ucd_mem']['mem_shared'] / $device_state['ucd_mem']['mem_total']) * 100);
$avai_perc = round(($device_state['ucd_mem']['mem_avail'] / $device_state['ucd_mem']['mem_total']) * 100);

    $graph_array = array();
    $graph_array['height'] = "100";
    $graph_array['width']  = "476";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['device']     = $device['device_id'];
    $graph_array['type']   = 'device_ucd_memory';
    $graph_array['from']     = $config['time']['day'];
    $graph_array['legend']   = "no";
    $graph = generate_graph_tag($graph_array);

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width']);
    $link = generate_url($link_array);

    $graph_array['width']  = "210";
    $overlib_content = generate_overlib_content($graph_array, $device['hostname'] . " - Memory Usage");

    echo(overlib_link($link, $graph, $overlib_content, NULL));

$percentage_bar            = array();
$percentage_bar['border']  = "#E25A00";
$percentage_bar['bg']      = "#f0f0f0";
$percentage_bar['width']   = "100%";
$percentage_bar['text']    = $avai_perc."%";
$percentage_bar['text_c']  = "#E25A00";
$percentage_bar['bars'][0] = array('percent' => $used_perc, 'colour' => '#E25A00', 'text' => $used_perc.'%');
$percentage_bar['bars'][1] = array('percent' => $cach_perc, 'colour' => '#f0e0a0', 'text' => '');
$percentage_bar['bars'][2] = array('percent' => $buff_perc, 'colour' => '#ff1a00', 'text' => '');
$percentage_bar['bars'][3] = array('percent' => $shar_perc, 'colour' => '#008fea', 'text' => '');

echo('<table width="100%" class="table-striped table-condensed-more">');
echo('  <tr>');
echo('    <td class="tablehead">RAM</td>');
echo('    <td colspan=8>');
echo(percentage_bar($percentage_bar));
echo('    </td>');
echo('  </tr>');


?>
  <tr class="syslog">
    <td><i style="font-size: 7px; line-height: 7px; background-color: #E25A00; border: 1px #aaa solid;">&nbsp;&nbsp;&nbsp;</i> Used</td>
    <td><?php echo(formatStorage($mem_used * 1024).' ('.$used_perc.'%)'); ?></td>
    <td><i style="font-size: 7px; line-height: 7px; background-color: #f0e0a0; border: 1px #aaa solid;">&nbsp;&nbsp;&nbsp;</i> Cached</td>
    <td><?php echo(formatStorage($device_state['ucd_mem']['mem_cached'] * 1024).' ('.$cach_perc.'%)'); ?></td>
    <td><i style="font-size: 7px; line-height: 7px; background-color: #ff1a00; border: 1px #aaa solid;">&nbsp;&nbsp;&nbsp;</i> Buffers</td>
    <td><?php echo(formatStorage($device_state['ucd_mem']['mem_buffer'] * 1024).' ('.$buff_perc.'%)'); ?></td>
    <td><i style="font-size: 7px; line-height: 7px; background-color: #008fea; border: 1px #aaa solid;">&nbsp;&nbsp;&nbsp;</i> Shared</td>
    <td><?php echo(formatStorage($device_state['ucd_mem']['mem_shared'] * 1024).' ('.$shar_perc.'%)'); ?></td>

  </tr>

<?php

$swap_used = $device_state['ucd_mem']['swap_total'] - $device_state['ucd_mem']['swap_avail'];
$swap_perc = round(($swap_used / $device_state['ucd_mem']['swap_total']) * 100);
$swap_free_perc = 100 - $swap_perc;

$background = get_percentage_colours('40');

$percentage_bar            = array();
$percentage_bar['border']  = "#356AA0";
$percentage_bar['bg']      = "#f0f0f0";
$percentage_bar['width']   = "100%";
$percentage_bar['text']    = $swap_free_perc."%";
$percentage_bar['text_c']  = "#356AA0";
$percentage_bar['bars'][0] = array('percent' => $swap_perc, 'colour' => '#356AA0', 'text' => $swap_perc.'%');

echo('  <tr>');
echo('    <td class="tablehead">Swap</td>');
echo('    <td colspan=8>');
echo(percentage_bar($percentage_bar));
echo('    </td>');
echo('  </tr>');

?>

  <tr class="syslog">
    <td><i style="font-size: 7px; line-height: 7px; background-color: #356AA0; border: 1px #aaa solid;">&nbsp;&nbsp;&nbsp;</i> Used</td>
    <td><?php echo(formatStorage($swap_used * 1024).' ('.$swap_perc.'%)'); ?></td>
    <td><i style="font-size: 7px; line-height: 7px; background-color: #ddd; border: 1px #aaa solid;">&nbsp;&nbsp;&nbsp;</i> Free</td>
    <td><?php echo(formatStorage($device_state['ucd_mem']['swap_avail'] * 1024).' ('.$swap_free_perc.'%)'); ?></td>
    <td><i style="font-size: 7px; line-height: 7px; background-color: #ddd; border: 1px #fff solid;">&nbsp;&nbsp;&nbsp;</i> Total</td>
    <td><?php echo(formatStorage($device_state['ucd_mem']['swap_total'] * 1024)); ?></td>
    <td></td>
    <td></td>
  </tr>
</table>

    </div>
  </div>
