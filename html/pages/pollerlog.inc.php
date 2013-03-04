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
      <th></th><th></th>
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

  // Reference the cache.
  $device = &$cache['devices']['id'][$id];


  if ($device['disabled'] == 1 && !$config['web_show_disabled']) { continue; }
  $proc['time']['poller'] = round((100 / $cache['devices']['timers']['polling']) * $device['last_polled_timetaken']);
  if ($device['last_polled_timetaken'] > ($proc['avg']['poller'] * 3)) { $proc['color']['poller'] = "danger"; }
  elseif ($device['last_polled_timetaken'] > ($proc['avg']['poller'] * 2)) { $proc['color']['poller'] = "warning"; }
  elseif ($device['last_polled_timetaken'] >= ($proc['avg']['poller'] / 2)) { $proc['color']['poller'] = "success"; }
  else { $proc['color']['poller'] = "info"; }
  $proc['time']['discovery'] = round((100 / $cache['devices']['timers']['discovery']) * $device['last_discovered_timetaken']);
  if ($device['last_discovered_timetaken'] > ($proc['avg']['discovery'] * 3)) { $proc['color']['discovery'] = "danger"; }
  elseif ($device['last_discovered_timetaken'] > ($proc['avg']['discovery'] * 2)) { $proc['color']['discovery'] = "warning"; }
  elseif ($device['last_discovered_timetaken'] >= ($proc['avg']['discovery'] / 2)) { $proc['color']['discovery'] = "success"; }
  else { $proc['color']['discovery'] = "info"; }

  // Poller times

  echo('    <tr class="'.$device['html_row_class'].'">
      <td style="width: 1px; max-width: 1px; background-color: '.$device['html_tab_colour'].'; margin: 0px; padding: 0px"></td>
      <td style="width: 1px; max-width: 1px;"></td>
      <td>'.generate_device_link($device).'</td>
      <td style="width: 12%;">
        <div class="progress progress-'.$proc['color']['poller'].' active" style="margin-bottom: 5px;"><div class="bar" style="text-align: right; width: '.$proc['time']['poller'].'%;"></div></div>
      </td>
      <td width="7%">
        '.$device['last_polled_timetaken'].'s
      </td>
      <td>'.format_timestamp($device['last_polled']).' </td>
      <td>'.formatUptime(time() - strtotime($device['last_polled']), 'shorter').' ago</td>');

  // Discovery times
  echo('
      <td  style="width: 12%;">
        <div class="progress progress-'.$proc['color']['discovery'].' active" style="margin-bottom: 5px;"><div class="bar" style="text-align: right; width: '.$proc['time']['discovery'].'%;"></div></div>
      </td>
      <td width="7%">
        '.$device['last_discovered_timetaken'].'s
      </td>
      <td>'.format_timestamp($device['last_discovered']).'</td>
      <td>'.formatUptime(time() - strtotime($device['last_discovered']), 'shorter').' ago</td>

    </tr>
');
}

echo('    <tr>
      <th colspan="4" style="text-align: right;">Total time for all devices:</th>
      <th colspan="3" style="text-align: left;">'.$cache['devices']['timers']['polling'].'s</th>
      <th></th>
      <th colspan="3" style="text-align: left;">'.$cache['devices']['timers']['discovery'].'s</th>
    </tr>
');

unset($proc);

?>
  </tbody>
</table>

