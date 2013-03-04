<?php

/**
 * Observium
 *
 *   This files is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @author     Dennis de Houx <info@all-in-one.be>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.0.1
 *
 */

$pagetitle[] = "Polling Information";

?>

<table class="table table-striped table-condensed table-bordered table-rounded" style="margin-top: 10px;">
  <thead>
    <tr>
      <th>Device</th>
      <th colspan=3>Last Polled</th>
      <th></th>
      <th colspan=3>Last Discovered</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
<?php

$proc['avg']['poller'] = round($cache['devices']['timers']['polling'] / count($cache['devices']['hostname']));
$proc['avg']['discovery'] = round($cache['devices']['timers']['discovery'] / count($cache['devices']['hostname']));

foreach($cache['devices']['hostname'] as $hostname=>$id) {
  if ($cache['devices']['id'][$id]['disabled'] == 1) { continue; }
  $proc['time']['poller'] = round((100 / $cache['devices']['timers']['polling']) * $cache['devices']['id'][$id]['last_polled_timetaken']);
  if ($cache['devices']['id'][$id]['last_polled_timetaken'] > ($proc['avg']['poller'] * 3)) { $proc['color']['poller'] = "danger"; }
  elseif ($cache['devices']['id'][$id]['last_polled_timetaken'] > ($proc['avg']['poller'] * 2)) { $proc['color']['poller'] = "warning"; }
  elseif ($cache['devices']['id'][$id]['last_polled_timetaken'] >= ($proc['avg']['poller'] / 2)) { $proc['color']['poller'] = "success"; }
  else { $proc['color']['poller'] = "info"; }
  $proc['time']['discovery'] = round((100 / $cache['devices']['timers']['discovery']) * $cache['devices']['id'][$id]['last_discovered_timetaken']);
  if ($cache['devices']['id'][$id]['last_discovered_timetaken'] > ($proc['avg']['discovery'] * 3)) { $proc['color']['discovery'] = "danger"; }
  elseif ($cache['devices']['id'][$id]['last_discovered_timetaken'] > ($proc['avg']['discovery'] * 2)) { $proc['color']['discovery'] = "warning"; }
  elseif ($cache['devices']['id'][$id]['last_discovered_timetaken'] >= ($proc['avg']['discovery'] / 2)) { $proc['color']['discovery'] = "success"; }
  else { $proc['color']['discovery'] = "info"; }
  $rowcolor = "";
  if ($cache['devices']['id'][$id]['status'] == 0) { $rowcolor = "error"; }
  if ($cache['devices']['id'][$id]['ignore'] == 1 && $cache['devices']['id'][$id]['status'] != 1) { $rowcolor = "warning"; }

  echo('    <tr class="'.$rowcolor.'">
      <td>'.generate_device_link($cache['devices']['id'][$id]).'</td>
      <td style="width: 12%;">
        <div class="progress progress-'.$proc['color']['poller'].' active" style="margin-bottom: 5px;"><div class="bar" style="text-align: right; width: '.$proc['time']['poller'].'%;"></div></div>
      </td>
      <td width="7%">
        '.$cache['devices']['id'][$id]['last_polled_timetaken'].'s</div>
      </td>
      <td>'.format_timestamp($cache['devices']['id'][$id]['last_polled']).'</td>
      <td>'.formatUptime(time() - strtotime($cache['devices']['id'][$id]['last_polled']), 'shorter').' ago</td>
      <td  style="width: 12%;">
        <div class="progress progress-'.$proc['color']['discovery'].' active" style="margin-bottom: 5px;"><div class="bar" style="text-align: right; width: '.$proc['time']['discovery'].'%;"></div></div>
      </td>
      <td width="7%">
        '.$cache['devices']['id'][$id]['last_discovered_timetaken'].'s
      </td>
      <td>'.format_timestamp($cache['devices']['id'][$id]['last_discovered']).' </td>
      <td>'.formatUptime(time() - strtotime($cache['devices']['id'][$id]['last_discovered']), 'shorter').' ago</td>
    </tr>
');
}

echo('    <tr class="info">
      <td colspan="2" style="text-align: right;">Total time for all devices:</td>
      <td colspan="3" style="text-align: left;">'.$cache['devices']['timers']['polling'].'s</td>
      <td></td>
      <td colspan="3" style="text-align: left;">'.$cache['devices']['timers']['discovery'].'s</td>
    </tr>
');

unset($proc);

?>
  </tbody>
</table>

