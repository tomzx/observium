<html>
<head>
  <link rel="stylesheet" href="css/bootstrap.css" />
  <link rel="stylesheet" href="css/sweetie.css" />
  <link rel="stylesheet" href="css/sprites.css" />

</head>
<body>
<?php

$icons = "sweetie-arrow-branch-bgr sweetie-arrow-branch-byr sweetie-arrow-branch-gyr sweetie-arrow-down sweetie-arrow-incident-blue sweetie-arrow-incident-green sweetie-arrow-incident-red sweetie-arrow-left sweetie-arrow-right sweetie-arrow-up sweetie-asset-black sweetie-asset-blue sweetie-asset-green sweetie-asset-grey sweetie-asset-red sweetie-asset-tan sweetie-asset-teal sweetie-asset-white sweetie-asset-yellow sweetie-badge-circle-check sweetie-badge-circle-cross sweetie-badge-circle-direction-down sweetie-badge-circle-direction-left sweetie-badge-circle-direction-right sweetie-badge-circle-direction-up sweetie-badge-circle-minus sweetie-badge-circle-plus sweetie-badge-circle-power sweetie-badge-square-check sweetie-badge-square-cross sweetie-badge-square-direction-down sweetie-badge-square-direction-left sweetie-badge-square-direction-right sweetie-badge-square-direction-up sweetie-badge-square-minus sweetie-badge-square-plus sweetie-badge-square-power sweetie-check sweetie-circle-black sweetie-circle-blue sweetie-circle-glass sweetie-circle-green sweetie-circle-grey sweetie-circle-metal sweetie-circle-paper sweetie-circle-red sweetie-circle-tan sweetie-circle-teal sweetie-circle-white sweetie-circle-wood sweetie-circle-yellow sweetie-clipboard-audit sweetie-clipboard-comment sweetie-clipboard-eye sweetie-clipboard-fingerprint sweetie-clipboard-report-bar sweetie-clipboard-search sweetie-clipboard-warning sweetie-comment sweetie-comment-chat sweetie-comment-inactive sweetie-cross sweetie-dice-red sweetie-dice-white sweetie-email sweetie-email-minus sweetie-email-plus sweetie-heart sweetie-lightening sweetie-minus sweetie-note sweetie-note-star sweetie-note-todo sweetie-note-todo-list sweetie-page sweetie-page-forum sweetie-page-pencil sweetie-page-star sweetie-paper-arrow-blue sweetie-paper-arrow-green sweetie-paper-arrow-red sweetie-paper-arrow-teal sweetie-paper-arrow-yellow sweetie-paper-calculate-percent sweetie-paper-control sweetie-paper-excerpt-blue sweetie-paper-excerpt-green sweetie-paper-excerpt-red sweetie-paper-excerpt-teal sweetie-paper-excerpt-yellow sweetie-paper-gavel sweetie-paper-workplan sweetie-pencil sweetie-person sweetie-person-heart sweetie-person-minus sweetie-person-plus sweetie-person-profile sweetie-plus sweetie-search sweetie-settings sweetie-shield-blue-broken sweetie-square-black sweetie-square-blue sweetie-square-glass sweetie-square-green sweetie-square-grey sweetie-square-metal sweetie-square-paper sweetie-square-red sweetie-square-tan sweetie-square-teal sweetie-square-white sweetie-square-wood sweetie-square-yellow sweetie-star sweetie-star-inactive sweetie-warning sweetie-zoom-in sweetie-zoom-out ";

$icons = shell_exec('/usr/bin/cat /home/observium/dev/html/css/sprites.css | /usr/bin/cut -f1 -d\'{\' | /usr/sed s/\.// | grep fugue\-[a-z] | /usr/bin/xargs');

foreach(explode(" ",$icons) as $icon)
{
  echo('<div style="margin: 2px; background-color: #f5f5f5; width: 260px; padding: 2px; float: left; height:16px; ">');
  echo('<a alt="'.$icon.'" class="'.$icon.'"><a> '.$icon);
  echo('</div>');
}
?>
</body>
</html>
