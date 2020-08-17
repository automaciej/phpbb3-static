<?php

// This is a minimal shim which redirects from where the forum was, to the
// archive.
require_once('redirection-data.php');

$found = false;

if (isset($_GET['f'])) {
  $f = $_GET['f'];
  $found = true;
}

if ($found) {
  header('Location: ' . $archive_base_url . $f . '/', true, 301);
  exit();
} else {
  header('HTTP/1.0 410 Gone');
}

?>
<!doctype html>
<html>
<head>
<title>This forum is gone</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h1>This forum does not exist in the archive.</h1>
<h2>HTTP 410</h2>
</body>
</html>
