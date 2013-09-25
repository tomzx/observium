<?php

if (!empty($agent_data['app']['postfix_mailgraph']))
{
  $postfix_mailgraph = $agent_data['app']['postfix_mailgraph'];

  foreach (explode("\n",$postfix_mailgraph) as $line)
  {
    list($item,$value) = explode(":",$line,2);
    $queue_data[trim($item)] = trim($value);
  }

  $old_rrd_filename = $config['rrd_dir']."/".$device['hostname']."/app-postfix-mailgraph-".$app['app_id'].".rrd";
  $rrd_filename = $config['rrd_dir']."/".$device['hostname']."/app-postfix-mailgraph.rrd";
  if (is_file($old_rrd_filename) && !is_file($rrd_filename)){ rename($old_rrd_filename, $rrd_filename); echo "Moved RRD"; }

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

  # Workaround for old agent script
  if (!isset($queue_data['rcvd'])) { $queue_data['rcvd'] = $queue_data['received']; }

  foreach (array('sent','rcvd','bounced','rejected','virus', 'spam', 'greylisted', 'delayed') as $key)
  {
    $rrd_values[] = (is_numeric($queue_data[$key]) ? $queue_data[$key] : "U");
  }
                                          
  rrdtool_update($rrd_filename, "N:" . implode(':', $rrd_values));
}
?>
