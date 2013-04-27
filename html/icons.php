<html>
<head>
  <link rel="stylesheet" href="css/bootstrap.css" />
  <link rel="stylesheet" href="css/sprite.css" />
</head>
<body>
<?php

$icons = '';
foreach (new SplFileObject('css/sprite.css') as $line)
{
  if (preg_match('/\.(oicon[\w\-]+)\s*{/', $line, $matches)) { $icons .= $matches[1] . ' '; }
}

foreach (new SplFileObject('css/bootstrap.css') as $line)
{
  if (preg_match('/\.(icon[\w\-]+)\s*{/', $line, $matches)) { $icons .= $matches[1] . ' '; }
}

foreach (explode(" ", $icons) as $icon)
{
  echo('<div style="margin: 2px; background-color: #f5f5f5; width: 260px; padding: 2px; float: left; height:16px; ">');
  echo('<a alt="'.$icon.'" class="'.$icon.'"><a> '.$icon);
  echo('</div>');
}
?>
</body>
</html>
