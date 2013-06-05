        <img src="graph.php?type=device_poller_perf&device=<?php echo($device['device_id']) ?>&operation=poll&width=1095&height=150&from=<?php echo($config['time']['week']); ?>&to=<?php echo($config['time']['now']); ?>" />


<div class="row">
  <div class="span6">
    <div class="well info_box">
      <div id="title"><a href="<?php echo(generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'health', 'metric' => 'storage'))); ?>">
        <i class="oicon-blocks"></i> Module Performance</a></div>
      <div id="content">

<table class="table table-hover table-striped table-bordered table-condensed table-rounded">
  <thead>
    <tr>
      <th>Module</th>
      <th colspan="2">Duration</th>
    </tr>
  </thead>
  <tbody>
<?php

arsort($device['state']['poller_mod_perf']);

foreach ($device['state']['poller_mod_perf'] as $module => $time)
{
 if($time > 0.01)
 {

   $perc = round($time / $device['last_polled_timetaken'] * 100, 2, 2);


  echo('    <tr>
      <td>'.$module.'</td>
      <td>'.$time.'s</td>
      <td>'.$perc.'%</td>
    </tr>');
  }
}

?>
      </tbody>
    </table>
  </div>
</div>
</div>

<div class="span6">
    <div class="well info_box">
      <div id="title"><a href="<?php echo(generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'health', 'metric' => 'storage'))); ?>">
        <i class="oicon-blocks"></i> Total Performance</a></div>
      <div id="content">

<table class="table table-hover table-striped table-bordered table-condensed table-rounded">
  <thead>
    <tr>
      <th>Time</th>
      <th>Duration</th>
    </tr>
  </thead>
  <tbody>
<?php

$times = dbFetchRows("SELECT * FROM `devices_perftimes` WHERE `operation` = 'poll' AND `device_id` = ? ORDER BY `start` DESC LIMIT 100", array($device['device_id']));

foreach ($times as $time)
{

  echo('    <tr>
      <td>'.format_unixtime($time['start']).'</td>
      <td>'.$time['duration'].'s</td>
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

$times = dbFetchRows('SELECT * FROM `devices_perftimes` WHERE `operation` = "discover" AND `device_id` = ? ORDER BY `start` DESC LIMIT 100', array($device['device_id']));

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
