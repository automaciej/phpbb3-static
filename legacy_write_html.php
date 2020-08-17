<?php

require_once('config.php');
require_once('common.php');

log_info("Writing HTML files:\n");

$forum_url = trim($forum_url, '/');
$forum_dir = trim($forum_dir, '/');
$target_dir = trim($target_dir, '/');

$forum_dir_valid = TRUE;

if (!is_dir($forum_dir)) {
  error_log("WARNING: forum directory is not valid: '$forum_dir'");
  error_log("         Smilies and attachments will not be copied.");
  $forum_dir_valid = FALSE;
}

if (!file_exists("./forum-data.json")) {
  error_log("ERROR: forum data not available. Run this command first:");
  error_log("       php extract");
  exit(1);
}

mkdir_p($target_dir, 0755);


$total_forums = 0;
$total_topics = 0;
$total_attachments = 0;
$total_attachment_errors = 0;

function copy_attachments($posts, $topic_dir, $tid) {
  global $forum_dir;
  global $target_dir;
  global $total_attachment_errors;
  global $total_attachments;
  
  // prevent copy warnings
  $old_error_reporting = error_reporting(E_ERROR);
  
  foreach ($posts as $post) {
	$attachments = $post['attachments'];
	if (!empty($attachments)) {
	  foreach ($attachments as $attachment) {
        $id = $attachment['id'];
        $physical_filename = $attachment['physical_filename'];
        $real_filename = $attachment['real_filename'];
        //$filetime => $attachment['filetime'];
		$source = $forum_dir . "/files/" . $physical_filename;
		$dest = $target_dir . '/' . $topic_dir . $real_filename;
		if (copy($source, $dest) !== FALSE) {
		  log_info("*");
		  $total_attachments++;
		} else {
		  error_log("\nERROR: cannot copy attachment '$source' to '$dest'");
		  error_log("       Attachment ID: $id / Topic ID: $tid");
		  $total_attachment_errors++;
		}
	  }
	}
  }
  error_reporting($old_error_reporting);
}

function generate_topics($extracted) {
  global $forum_name, $forum_url;
  global $archive_base_url;
  global $total_topics;
  global $forum_dir_valid;

  $sitemap = array();
  $topics = $extracted['topics'];
  $forums = $extracted['forums'];

  log_info("Writing topics...");

  foreach ($topics as $tid => $topic) {
    $fid = $topics[$tid]['fid'];
    $var = array();
    $var['forum_name'] = $forum_name;
    if (!empty($forums[$fid]['title'])) {
      $var['forum_title'] = $forums[$fid]['title'];
    } else {
      $var['forum_title'] = '(unknown forum)';
    }
    if (!empty($topics[$tid]['title'])) {
      $var['title'] = $topics[$tid]['title'];
    } else {
      $var['title'] = '(unknown topic)';
    }
    $var['slug'] = slug($var['title']);
    $var['tid'] = $tid;
    $var['url'] = $forum_url . '/viewtopic.php?t=' . $tid;
    $var['posts'] = array();
    $var['lastmod'] = $topics[$tid]['lastmod'];

    $var['posts'] = $topic['posts'];
    // Generate a redirection page. We might not know the topic slug when
    // linking. In such case we land in the slug-less page which is a redirect
    // to the slugged URL, with content.
    $content = template_get($var, 'topic-redirect.tpl.php');
    write_content($fid . '/' . $tid . '/index.html', $content);

    $post_rel_url = $fid . '/' . $tid . '/' . $var['slug'] . '/';
    array_push($sitemap, array(
      'loc' => $archive_base_url . $post_rel_url,
      'lastmod' => $var['lastmod'],
    ));

    $content = template_get($var, 'topic.tpl.php');
    write_content($post_rel_url . '/index.html', $content);

    log_info(" $tid");
	
	if ($forum_dir_valid) {
	  // saving attachments
	  copy_attachments($topic['posts'], $post_rel_url, $tid);
	}
	
	$total_topics++;
  }

  log_info("\n");

  log_info("Writing sitemap.xml ...");
  $var = array(
    'urlset' => $sitemap,
  );
  $content = template_get($var, 'sitemap.tpl.php');
  write_content('sitemap.xml', $content);
  log_info("done.\n");
}

function generate_forums($extracted) {
  global $forum_name, $forum_description;
  global $total_forums;

  $forums = $extracted['forums'];
  $topics = $extracted['topics'];
  log_info("Loading forums...");
  foreach ($forums as $fid => $forum) {
    if (!empty($forums[$fid]['title'])) {
      $forum_title = $forums[$fid]['title'];
    } else {
      $forum_title = '(unknown forum)';
    }
    $var = array(
      'topics'            => $topics,
      'list'              => $forums[$fid]['topics'],
      'forum_name'        => $forum_name,
      'forum_title'       => $forum_title,
      'forum_description' => $forum_description
    );

    $content = template_get($var, 'forum.tpl.php');
    write_content($fid . '/index.html', $content);

    log_info(" $fid");
	$total_forums++;
  }
  log_info("\n");

}

function generate_main($extracted) {
  global $forum_name, $forum_description;

  log_info("Creating index...");

  // Content
  $var = array(
    'categories'        => $extracted['categories'],
    'forums'            => $extracted['forums'],
    'forum_name'        => $forum_name,
    'forum_description' => $forum_description
  );
  $content = template_get($var, 'main.tpl.php');

  write_content('index.html', $content);

  log_info("done\n");
}

function copy_smilies() {
	global $target_dir;
	global $forum_dir;
	global $forum_dir_valid;

	if ($forum_dir_valid) {
		$source_dir = $forum_dir . "/images/smilies";
		if (!is_dir($source_dir)) {
			error_log("ERROR: forum smiley directory does not exist: $source_dir");
		} else {
			log_info("Copying smilies...");
			$dest_dir = $target_dir . "/images";
			mkdir_p($dest_dir);
			recurse_copy($source_dir, $dest_dir . "/smilies");
			log_info("done\n");
		}
	}
}


function copy_templates() {
	global $target_dir;

	log_info("Copying scripts and images...");
	$source_dir = "./templates/res";
	$dest_dir = $target_dir;
	recurse_copy($source_dir, $dest_dir);
	log_info("done\n");
}


log_info("Loading forum-data.json...");
$extracted = json_decode(file_get_contents("./forum-data.json"), true);
log_info("done.\n");

generate_main($extracted);
generate_forums($extracted);
generate_topics($extracted);
copy_templates();
copy_smilies();

log_info("\nStatistics:\n");
log_info("- forums: $total_forums\n");
log_info("- topics: $total_topics\n");
log_info("- attachments: $total_attachments ($total_attachment_errors failed)\n");
log_info("Successfully created forum archive in: $target_dir\n");
log_info("\n");

