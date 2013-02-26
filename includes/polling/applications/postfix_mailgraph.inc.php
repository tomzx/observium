<?php

if (!empty($agent_data['app']['postfix_mailgraph']))
{
    $postfix_mailgraph = $agent_data['app']['postfix_mailgraph'];

    foreach (explode("\n",$postfix_mailgraph) as $line) {
        list($item,$value) = explode(":",$line,2);
        $queue_data[trim($item)] = trim($value);
        print trim($item) . " " . trim($value) . "\n";
    }

    $rrd_filename = $config['rrd_dir']."/".$device['hostname']."/app-postfix-mailgraph-".$app['app_id'].".rrd";

    if (!is_file($rrd_filename))
    {
        rrdtool_create($rrd_filename, "--step 300 \
                                        DS:sent:COUNTER:600:0:1000000 \
                                        DS:received:COUNTER:600:0:1000000 \
                                        DS:bounced:COUNTER:600:0:1000000 \
                                        DS:rejected:COUNTER:600:0:1000000 \
                                        DS:virus:COUNTER:600:0:1000000 \
                                        DS:spam:COUNTER:600:0:1000000 \
                                        DS:greylisted:COUNTER:600:0:1000000 \
                                        DS:delayed:COUNTER:600:0:1000000 ".$config['rrd_rra']);

    }

   rrdtool_update($rrd_filename, "N:".$queue_data['sent'].":".$queue_data['received'].":".$queue_data['bounced'].":".$queue_data['rejected'].":".$queue_data['virus'].":".$queue_data['spam'].":".$queue_data['greylisted'].":".$queue_data['delayed']);
}
?>
