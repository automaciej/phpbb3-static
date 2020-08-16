<?php

require_once('config.php');
require_once('common.php');

$forum_url = trim($forum_url, '/');

function generate_topics($extracted) {
  global $forum_name, $forum_url;
  global $archive_base_url;

  $sitemap = array();
  $topics = $extracted['topics'];
  $forums = $extracted['forums'];

  log_info("Topics:");

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
  }

  log_info("\n");

  log_info("Sitemap: ");
  $var = array(
    'urlset' => $sitemap,
  );
  $content = template_get($var, 'sitemap.tpl.php');
  write_content('sitemap.xml', $content);
  log_info("done.\n");
}

function generate_forums($extracted) {
  global $forum_name, $forum_description;

  $forums = $extracted['forums'];
  $topics = $extracted['topics'];
  log_info("Forum index:");
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
  }
  log_info("\n");

}

function generate_main($extracted) {
  global $forum_name, $forum_description;

  // Content
  $var = array(
    'categories'        => $extracted['categories'],
    'forums'            => $extracted['forums'],
    'forum_name'        => $forum_name,
    'forum_description' => $forum_description
  );
  $content = template_get($var, 'main.tpl.php');

  write_content('index.html', $content);

  log_info("Index: index.html\n");
}

$extracted = json_decode(file_get_contents("./forum-data.json"), true);
generate_main($extracted);
generate_forums($extracted);
generate_topics($extracted);
