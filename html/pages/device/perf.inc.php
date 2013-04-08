<div class="row">
<div class="span6">

<h4 style="margin-bottom: 10px;">Poller Times</h4>

<!--- <img src="graph-device-perf.php?device_id=<?php echo($device['device_id']) ?>&operation=poll&width=570&height=200"/> -->
<img src="graph.php?type=device_poller_perf&device=<?php echo($device['device_id']) ?>&operation=poll&width=480&height=150" />

<table class="table table-hover table-striped table-bordered table-condensed table-rounded">
  <thead>
    <tr>
      <th>Time</th>
      <th>Duration</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
<?php

$times = dbFetchRows("SELECT * FROM `devices_perftimes` WHERE `operation` = 'poll' AND `device_id` = ? ORDER BY `start` DESC", array($device['device_id']));

foreach ($times as $time)
{

  echo('    <tr>
      <td>'.format_unixtime($time['start']).'</td>
      <td>'.$time['duration'].'s</td>
      <td></td>
    </tr>');

}

?>
  </tbody>
</table>
</div>
<div class="span6">

<h4  style="margin-bottom: 10px;">Discovery Times</h4>

<table class="table table-hover table-striped table-bordered table-condensed table-rounded">
  <thead>
    <tr>
      <th>Time</th>
      <th>Duration</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
<?php

$times = dbFetchRows('SELECT * FROM `devices_perftimes` WHERE `operation` = "discover" AND `device_id` = ? ORDER BY `start` DESC', array($device['device_id']));

foreach ($times as $time)
{

  echo('    <tr>
      <td>'.format_unixtime($time['start']).'</td>
      <td>'.$time['duration'].'s</td>
      <td></td>
    </tr>');

}

?>
  </tbody>
</table>

</div>
</div>
