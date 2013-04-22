<?php

    // Display devices as a list in detailed format

    echo('<table class="table table-hover table-striped table-bordered table-condensed table-rounded" style="margin-top: 10px;">');
    echo("  <thead>\n");
    echo("    <tr>\n");
    echo("      <th></th>\n");
    echo("      <th></th>\n");
    echo("      <th>Device/Location</th>\n");
    echo("      <th></th>\n");
    echo("    </tr>\n");
    echo("  </thead>\n");

    foreach ($devices as $device)
    {
      if (device_permitted($device['device_id']))
      {
        if (!$location_filter || $device['location'] == $location_filter)
        {
          include("includes/hostbox-status.inc.php");
        }
      }
    }
    echo("</table>");

