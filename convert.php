<?php

require_once('config.php');
require_once('common.php');

$forum_url = trim($forum_url, '/');

function get_forums_tree() {
	global $db;
	global $categories, $forums_tree, $forums_top;
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

			$forums_top[] = $row['forum_id'];
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

		$cat_ids = array();

		while (list($fid, $forum) = each($forums_tree)) {
			$parent_id = $forum['parent_id'];

			if ($parent_id != 0) {

				while ($parent_id != 0) {
					$cat_id = $parent_id;
					$parent_id = $forums_tree[$parent_id]['parent_id'];
				}

				$forums_tree[$fid]['cat_id'] = $cat_id;

			}
			else {
				$cat_ids[] = $fid;
			}

		}

		foreach ($cat_ids as $cat_id) {
			foreach ($forums_tree[$cat_id]['children'] as $fid) {
				$forums_top[] = $fid;
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

	log_info("Topics:");

	while (list($tid, $topic) = each($topics)) {
		
		$fid = $topics[$tid]['fid'];
		$var = array();
		$var['forum_name'] = $forum_name;
		$var['forum_title'] = $forums[$fid]['title'];
		$var['title'] = $topics[$tid]['title'];
		$var['tid'] = $tid;
		$var['url'] = $forum_url . '/viewtopic.php?t=' . $tid;
		$var['posts'] = array();

		if ($phpbb_version == PHPBB2) {
			$res = $db->query('SELECT p.post_id, p.poster_id, p.post_username, u.username, p.post_time, pt.post_subject, pt.post_text, pt.bbcode_uid FROM '.$db_prefix.'posts p LEFT JOIN '.$db_prefix.'users u ON p.poster_id=u.user_id LEFT JOIN '.$db_prefix.'posts_text pt ON p.post_id=pt.post_id WHERE p.topic_id=' . $tid . ' ORDER BY p.post_time ASC');
		}
		else if ($phpbb_version == PHPBB3) {
			$res = $db->query('SELECT p.post_id, p.poster_id, p.post_username, u.username, p.post_time, p.post_subject, p.post_text, p.bbcode_uid FROM '.$db_prefix.'posts p LEFT JOIN '.$db_prefix.'users u ON p.poster_id=u.user_id WHERE p.topic_id=' . $tid . ' ORDER BY p.post_time ASC');
		}

		foreach ($res as $row) {
			$var['posts'][] = array(
				'username'   => $row['username'],
				'post_text'  => $row['post_text'],
				'post_time'  => $row['post_time'],
				'bbcode_uid'  => $row['bbcode_uid'],
			);
		}
		

		$content = template_get($var, 'topic.tpl.php');
		write_content($fid . '/t-' . $tid . '.html', $content);

		log_info(" $tid");
	}

	log_info("\n");
}

function generate_forums() {
	global $db;
	global $forums, $topics;
	global $filter_forum, $filter_topic;
	global $db_prefix;
	global $forum_name, $forum_description;

	$res = $db->query('SELECT t.forum_id, t.topic_id, t.topic_title, t.topic_time, t.topic_replies, u.username FROM '.$db_prefix.'topics t LEFT JOIN '.$db_prefix.'users u ON t.topic_poster=u.user_id WHERE t.topic_moved_id = 0 ORDER BY t.topic_time DESC');

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
			'author'  => $row['username']
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
	global $filter_forum, $filter_topic;
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
		$cid = $row['cat_id'];
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
			'cid'     => $cid,
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
$forums_top = array();

try {
  generate_main();
  generate_forums();
  generate_topics();
} catch(PDOException $ex) {
  echo "An Error occured! " . $ex->getMessage();
  throw $ex;
}
