<?php

// This file generates redirection data for shim implementations of
// viewforum.php and viewtopic.php.

require_once('config.php');
require_once('common.php');

function array_dump($a) {
  $output = 'array(';
  foreach ($a as $key => $value) {
    $output .= "'" . $key . "' => array('" . $value[0] . "', '" . $value[1] . "'),\n";
  }
  $output .= ")\n";
  return $output;
}

log_info("Loading JSON… ");
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
$output .= '$by_post_id = ' . array_dump($by_post_id) . ";\n";
$output .= '$by_topic_id = ' . array_dump($by_topic_id) . ";\n";
$output .= "?>\n";

$f = fopen('redirection-data.php', 'w');
fputs($f, $output);
fclose($f);
log_info("Wrote redirection-data.php\n");

?>
