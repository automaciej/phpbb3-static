<?php

// This is a minimal shim which redirects from where the forum was, to the
// archive.

// This file needs to know which post numbers correspond to which topic numbers,
// and which topic numbers correspond to which forums.
require_once('redirection-data.php');

//$archive_base_url = '<please set>/';
//if this is in the static folder, use this:
$archive_base_url = '';

$found = false;

if (isset($_GET['p'])) {
  $p = $_GET['p'];
  if (isset($by_post_id[$p])) {
    list($f, $t) = $by_post_id[$_GET['p']];
    $found = true;
  }
} else if (isset($_GET['t'])) {
  $t = $_GET['t'];
  if (isset($by_topic_id[$t])) {
    list($f, $t) = $by_topic_id[$_GET['t']];
    $found = true;
  }
}

if ($found) {
  header('Location: ' . $archive_base_url . $f . '/' . $t . '/', true, 301);
  exit();
} else {
  header('HTTP/1.0 410 Gone');
}

?>
<!doctype html>
<html>
<head>
<title>This topic is gone</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h1>This topic does not exist in the archive.</h1>
<h2>HTTP 410</h2>
</body>
</html>
