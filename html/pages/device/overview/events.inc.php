   <div class="well info_box">
      <div class="title"><a href="<?php echo(generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'logs', 'section' => 'eventlog'))); ?>">
        <i class="oicon-clipboard-audit"></i> Events</a></div>
      <div class="content">
<?php
      print_events(array('device' => $device['device_id'], 'pagesize' => 15, 'short' => TRUE));
?>
    </div>
  <div>

