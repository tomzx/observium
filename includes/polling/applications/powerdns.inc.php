<?php

if (!empty($agent_data['app']['powerdns']))
{
  foreach (explode(",",$agent_data['app']['powerdns']) as $line)
  {
    list($key,$value) = explode("=",$line,2);
    $powerdns[$key] = $value;
  }

  # FIXME should we really use app_id here? means if app is disabled, then reenabled, we will lose the data as a new id will be created...
  $rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-powerdns-".$app['app_id'].".rrd";
  $rrd_string = '';

  foreach(array('corrupt-packets', 'deferred-cache-inserts', 'deferred-cache-lookup', 'latency', 'packetcache-hit', 'packetcache-miss', 'packetcache-size', 'qsize-q',
    'query-cache-hit', 'query-cache-miss', 'recursing-answers', 'recursing-questions', 'servfail-packets', 'tcp-answers', 'tcp-queries', 'timedout-packets', 'udp-answers',
    'udp-queries', 'udp4-answers', 'udp4-queries', 'udp6-answers', 'udp6-queries') as $key)
  {
    $rrd_values[] = (is_numeric($powerdns[$key]) ? $powerdns[$key] : "U");
  }

  if (!is_file($rrd_filename))
  {
    rrdtool_create($rrd_filename, "--step 300 \
        DS:corruptPackets:DERIVE:600:0:125000000000 \
        DS:def_cacheInserts:DERIVE:600:0:125000000000 \
        DS:def_cacheLookup:DERIVE:600:0:125000000000 \
        DS:latency:DERIVE:600:0:125000000000 \
        DS:pc_hit:DERIVE:600:0:125000000000 \
        DS:pc_miss:DERIVE:600:0:125000000000 \
        DS:pc_size:DERIVE:600:0:125000000000 \
        DS:qsize:DERIVE:600:0:125000000000 \
        DS:qc_hit:DERIVE:600:0:125000000000 \
        DS:qc_miss:DERIVE:600:0:125000000000 \
        DS:rec_answers:DERIVE:600:0:125000000000 \
        DS:rec_questions:DERIVE:600:0:125000000000 \
        DS:servfailPackets:DERIVE:600:0:125000000000 \
        DS:q_tcpAnswers:DERIVE:600:0:125000000000 \
        DS:q_tcpQueries:DERIVE:600:0:125000000000 \
        DS:q_timedout:DERIVE:600:0:125000000000 \
        DS:q_udpAnswers:DERIVE:600:0:125000000000 \
        DS:q_udpQueries:DERIVE:600:0:125000000000 \
        DS:q_udp4Answers:DERIVE:600:0:125000000000 \
        DS:q_udp4Queries:DERIVE:600:0:125000000000 \
        DS:q_udp6Answers:DERIVE:600:0:125000000000 \
        DS:q_udp6Queries:DERIVE:600:0:125000000000 ".$config['rrd_rra']);
  }

  rrdtool_update($rrd_filename, "N:" . implode(':', $rrd_values));
}

?>
