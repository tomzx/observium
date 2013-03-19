<?php

if ($config['enable_printers'])
{
  $toner_data = dbFetchRows("SELECT * FROM toner WHERE device_id = ?", array($device['device_id']));

  foreach ($toner_data as $toner)
  {
    echo("Checking toner " . $toner['toner_descr'] . "... ");

    $tonerperc = round(snmp_get($device, $toner['toner_oid'], "-OUqnv") / $toner['toner_capacity'] * 100);

    $tonerrrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("toner-" . $toner['toner_index'] . ".rrd");

    if (!is_file($tonerrrd))
    {
      rrdtool_create($tonerrrd,"--step 300 \
      DS:toner:GAUGE:600:0:20000 ".$config['rrd_rra']);
    }

    echo($tonerperc . " %\n");

    rrdtool_update($tonerrrd,"N:$tonerperc");

    #FIXME should "alert" for toner out... :)

    # Log toner swap
    if ($tonerperc > $toner['toner_current'])
    {
      log_event('Toner ' . $toner['toner_descr'] . ' was replaced (new level: ' . $tonerperc . '%)', $device, 'toner', $toner['toner_id']);
    }

    dbUpdate(array('toner_current' => $tonerperc, 'toner_capacity' => $toner['toner_capacity']), 'toner', '`toner_id` = ?', array($toner['toner_id']));
  }
  
  if ($device['type'] == 'printer')
  {
    $oid = get_dev_attrib($device, 'pagecount_oid');

    if ($oid)
    {
      echo("Checking page count... ");
      $pages = snmp_get($device, $oid, "-OUqnv");

      $pagecountrrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("pagecount.rrd");

      if (!is_file($pagecountrrd))
      {
        rrdtool_create($pagecountrrd,"--step 300 \
        DS:pagecount:GAUGE:600:0:U ".$config['rrd_rra']);
      }

      set_dev_attrib($device, "pagecounter", $pages);
      rrdtool_update($pagecountrrd,"N:$pages");

      echo("$pages\n");
    }
  }
}

?>
