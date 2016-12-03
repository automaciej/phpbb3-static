<?php

require_once('config.php');
require_once('common.php');

$forum_url = trim($forum_url, '/');

function get_forums_tree() {
	global $db;
	global $categories, $forums_tree;
	global $phpbb_version;
	global $db_prefix;

	if ($phpbb_version == PHPBB2) {
		$res = $db->query('SELECT forum_id, cat_id FROM '.$db_prefix.'forums');

		foreach ($res as $row) {
			$forums_tree[$row['forum_id']] = array(
				'parent_id' => -1,
				'cat_id'    => $row['cat_id'],
				'children'  => array()
			);
		}
	}
	else if ($phpbb_version == PHPBB3) {
		$res = $db->query('SELECT forum_id, parent_id FROM '.$db_prefix.'forums');

		foreach ($res as $row) {
			$forums_tree[$row['forum_id']] = array(
				'parent_id' => $row['parent_id'],
				'children'  => array()
			);
		}

		while (list($fid, $forum) = each($forums_tree)) {
			$parent_id = $forum['parent_id'];

			if ($parent_id != 0) {

				while ($parent_id != 0) {
					$cat_id = $parent_id;
					$parent_id = $forums_tree[$parent_id]['parent_id'];
				}

				$forums_tree[$fid]['cat_id'] = $cat_id;

			}

		}

	}

}

function generate_topics() {
	global $db;
	global $topics, $forums;
	global $db_prefix;
	global $forum_name, $forum_url;
	global $phpbb_version;
	global $archive_base_url;
	global $bb;

	$res = $db->query(
		'SELECT config_value FROM ' . $db_prefix .
		"config WHERE config_name = 'smilies_path';");
	$smilies_path = $res->fetch()['config_value'];

	$sitemap = array();

	log_info("Topics:");

	while (list($tid, $topic) = each($topics)) {
		
		$fid = $topics[$tid]['fid'];
		$var = array();
		$var['forum_name'] = $forum_name;
		$var['forum_title'] = $forums[$fid]['title'];
		$var['title'] = $topics[$tid]['title'];
		$var['slug'] = slug($topics[$tid]['title']);
		$var['tid'] = $tid;
		$var['url'] = $forum_url . '/viewtopic.php?t=' . $tid;
		$var['posts'] = array();
		$var['lastmod'] = $topics[$tid]['lastmod'];

		if ($phpbb_version == PHPBB2) {
			$res = $db->query('SELECT p.post_id, p.poster_id, p.post_username, u.username, p.post_time, pt.post_subject, pt.post_text, pt.bbcode_uid FROM '.$db_prefix.'posts p LEFT JOIN '.$db_prefix.'users u ON p.poster_id=u.user_id LEFT JOIN '.$db_prefix.'posts_text pt ON p.post_id=pt.post_id WHERE p.topic_id=' . $tid . ' ORDER BY p.post_time ASC');
		}
		else if ($phpbb_version == PHPBB3) {
			$res = $db->query('SELECT p.post_id, p.poster_id, p.post_username, u.username, p.post_time, p.post_subject, p.post_text, p.bbcode_uid FROM '.$db_prefix.'posts p LEFT JOIN '.$db_prefix.'users u ON p.poster_id=u.user_id WHERE p.topic_id=' . $tid . ' ORDER BY p.post_time ASC');
		}

		foreach ($res as $row) {
			// Get the post from the live forum. This is a hacky approach. Ideally
			// we'd remove the dependency on Python and do it in pure PHP.
			system('./get_post.py ' . $forum_url . ' ' . $row['post_id'] . ' output.html');
			$fd = fopen('output.html', 'r') or die('Unable to open file output.html');
			$post_length = filesize('output.html');
			unlink('output.html');
			if ($post_length > 0) {
				$post_text = stream_get_contents($fd, $post_length);
			} else {
				// We got a zero-length file. Let's try to parse the database
				// representation that we've retrieved from the database. If there are
				// links in it, they will be broken, but it's better than nothing.
				$post_text = $row['post_text'];
				$post_text = str_replace(':' . $row['bbcode_uid'], '', $post_text);
				$post_text = preg_replace('/\[(\/?)code:\d*\]/', '[\1code]', $post_text);
				$post_text = nl2br($bb->qParse($post_text));
			}
			fclose($fd);
			$var['posts'][] = array(
				'username'   => $row['username'],
				// 'post_text'  => $row['post_text'],
				'post_text'  => $post_text,
				'post_time'  => $row['post_time'],
				'bbcode_uid'  => $row['bbcode_uid'],
				'post_id'  => $row['post_id'],
			);
		}
		
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

		// Fix the smilies paths. In an phpBB installation, links to smilies start
		// from the top level. In the case of the archive, topics are 3 levels down,
		// when you cound slashes. So if images are in the same place as previously,
		// we need to go 3 levels up to find them.
		$content = str_replace('src="./' . $smilies_path, 'src="../../../' . $smilies_path, $content);
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

function generate_forums() {
	global $db;
	global $forums, $topics;
	global $filter_forum;
	global $db_prefix;
	global $forum_name, $forum_description;

	$res = $db->query('SELECT t.forum_id, t.topic_id, t.topic_title, t.topic_time, t.topic_replies, u.username, t.topic_time FROM '.$db_prefix.'topics t LEFT JOIN '.$db_prefix.'users u ON t.topic_poster=u.user_id WHERE t.topic_moved_id = 0 ORDER BY t.topic_time DESC');

	foreach ($res as $row) {
		$fid = $row['forum_id'];

		if (in_array($fid, $filter_forum)) {
			continue;
		}

		$topics[$row['topic_id']] = array(
			'fid'     => $fid,
			'title'   => $row['topic_title'],
			'time'    => $row['topic_time'],
			'replies' => $row['topic_replies'],
			'author'  => $row['username'],
			'lastmod' => gmdate('Y-m-d\TH:i:s\Z', $row['topic_time']),
		);
		$forums[$fid]['topics'][] = $row['topic_id'];
	}

	log_info("Forum index:");
	while (list($fid, $forum) = each($forums)) {
		$var = array(
			'topics'            => $topics,
			'list'              => $forums[$fid]['topics'],
			'forum_name'        => $forum_name,
			'forum_title'       => $forums[$fid]['title'],
			'forum_description' => $forum_description
		);

		$content = template_get($var, 'forum.tpl.php');
		write_content($fid . '/index.html', $content);

		log_info(" $fid");
	}
	log_info("\n");

}

function generate_main() {
	global $db;
	global $categories, $forums, $forums_tree;
	global $filter_forum;
	global $db_prefix;
	global $forum_name, $forum_description;
	global $phpbb_version;

	//Categories
	if ($phpbb_version == PHPBB2) {
		$res = $db->query('SELECT cat_id, cat_title FROM '.$db_prefix.'categories ORDER BY cat_order');
	}
	else if ($phpbb_version == PHPBB3) {
		//FIXME: fix ordering
		$res = $db->query('SELECT forum_id AS cat_id, forum_name AS cat_title FROM '.$db_prefix.'forums WHERE parent_id=0 ORDER BY left_id');
	}

	foreach ($res as $row) {
		$categories[$row['cat_id']] = array(
			'title'  => $row['cat_title'],
			'forums' => array()
		);
	}

	//Forums
	get_forums_tree();

	if ($phpbb_version == PHPBB2) {
		$res = $db->query('SELECT forum_id, forum_name, forum_posts, forum_topics FROM '.$db_prefix.'forums ORDER BY forum_order');
	}
	else if ($phpbb_version == PHPBB3) {
		//FIXME: fix ordering
		$res = $db->query('SELECT forum_id, forum_name, forum_posts, forum_topics FROM '.$db_prefix.'forums WHERE parent_id<>0 ORDER BY left_id');
	}

	foreach ($res as $row) {
		$fid = $row['forum_id'];

		if (in_array($fid, $filter_forum)) {
			continue;
		}

		$cat_id = $forums_tree[$fid]['cat_id'];

		$forums[$fid] = array(
			'title'   => $row['forum_name'],
			'nposts'  => $row['forum_posts'],
			'ntopics' => $row['forum_topics'],
			'topics'  => array()
		);

		$categories[$cat_id]['forums'][] = $fid;
	}

	// Content
	$var = array(
		'categories'        => $categories,
		'forums'            => $forums,
		'forum_name'        => $forum_name,
		'forum_description' => $forum_description
	);
	$content = template_get($var, 'main.tpl.php');

	write_content('index.html', $content);

	log_info("Index: index.html\n");

}

$db = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8mb4', $db_user, $db_pass);

$categories = array();
$forums = array();
$topics = array();
$forums_tree = array();

try {
  generate_main();
  generate_forums();
  generate_topics();
} catch(PDOException $ex) {
  echo "An Error occured! " . $ex->getMessage();
  throw $ex;
}
