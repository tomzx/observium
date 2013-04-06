<html>
<head>
  <link rel="stylesheet" href="css/bootstrap.css" />
  <link rel="stylesheet" href="css/sweetie.css" />
  <link rel="stylesheet" href="css/sprites.css" />

</head>
<body>
<?php

/// FIXME. :D -- mike
$icons = '';
foreach (new SplFileObject('css/sprites.css') as $line)
{
  if (preg_match('/\.(oicon[\w\-]+)\s*{/', $line, $matches)) { $icons .= $matches[1] . ' '; }
}

foreach(explode(" ",$icons) as $icon)
{
  echo('<div style="margin: 2px; background-color: #f5f5f5; width: 260px; padding: 2px; float: left; height:16px; ">');
  echo('<a alt="'.$icon.'" class="'.$icon.'"><a> '.$icon);
  echo('</div>');
}
?>
</body>
</html>
