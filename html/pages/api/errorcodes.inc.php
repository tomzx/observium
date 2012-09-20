<h2 style="margin-bottom: 10px;">Simple Observium API - Error codes</h2>
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>Code</th>
      <th>Message</th>
    </tr>
  </thead>
  <tbody>
<?php

include_once("includes/api/errorcodes.inc.php");

foreach($errorcodes as $item=>$value) {
  echo("<tr><td>".$value['code']."</td><td>".$value['msg']."</td></tr>");
}

?>
  </tbody>
</table>