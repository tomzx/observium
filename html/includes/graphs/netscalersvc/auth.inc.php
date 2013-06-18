<?php

if (is_numeric($vars['id']))
{

  $svc = dbFetchRow("SELECT * FROM `netscaler_services` AS I, `devices` AS D WHERE I.svc_id = ? AND I.device_id = D.device_id", array($vars['id']));

  if (is_numeric($svc['device_id']) && ($auth || device_permitted($svc['device_id'])))
  {
    $device = device_by_id_cache($svc['device_id']);

    $rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("nscaler-svc-".$svc['svc_name'].".rrd");

    $title  = generate_device_link($device);
    $title .= " :: Netscaler VServer :: " . htmlentities($svc['svc_name']);
    $auth = TRUE;
  }
}

?>
