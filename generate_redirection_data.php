<?php

// This file generates redirection data for shim implementations of
// viewforum.php and viewtopic.php.

require_once('config.php');
require_once('common.php');

log_info("Generating redirection data:\n");

$target_dir = trim($target_dir, '/');
mkdir_p($target_dir, 0755);

if (!file_exists("./forum-data.json")) {
  error_log("ERROR: forum data not available. Run this command first:");
  error_log("       php extract");
  exit(1);
}

function array_dump($a) {
  $output = 'array(';
  foreach ($a as $key => $value) {
    $output .= "'" . $key . "' => array('" . $value[0] . "', '" . $value[1] . "'),\n";
  }
  $output .= ")\n";
  return $output;
}

log_info("Loading forum-data.json...");
$extracted = json_decode(file_get_contents("./forum-data.json"), true);
log_info("done.\n");

$by_post_id = array();
$by_topic_id = array();
// Forum and topic by post ID
foreach ($extracted['topics'] as $tid => $topic) {
  $fid = $topic['fid'];
  foreach ($topic['posts'] as $post) {
    $by_post_id[(string)$post['post_id']] = array($fid, (string)$tid);
  }
  $by_topic_id[(string)$tid] = array($fid, (string)$tid);
}

$output = "<?php\n";
$output .= '$archive_base_url = "' . $archive_base_url . '";' . "\n";
$output .= '$by_post_id = ' . array_dump($by_post_id) . ";\n";
$output .= '$by_topic_id = ' . array_dump($by_topic_id) . ";\n";
$output .= "?>\n";

log_info("Writing $target_dir/redirection-data.php...");
$f = fopen($target_dir . '/redirection-data.php', 'w');
fputs($f, $output);
fclose($f);
log_info("done\n");

log_info("Copying viewforum.php and viewtopic.php to $target_dir...");
copy("./templates/viewforum.php", $target_dir . "/viewforum.php");
copy("./templates/viewtopic.php", $target_dir . "/viewtopic.php");
log_info("done\n\n");

?>
