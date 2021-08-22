<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html><head><title><?=$forum_title;?> - <?=$forum_name;?></title>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript" src="../jquery.tablesorter.js"></script>
<script type="text/javascript" src="../jquery.tablesorter.pager.js"></script>
<script type="text/javascript" src="../forum.js"></script>
<link rel="stylesheet" type="text/css" href="../forum.css"/>
</head><body onload="load('forum')">

<div id="header">
<p class="h"><a href="../"><?=$forum_name;?></a></p>
<p class="d"><?=$forum_description;?></p>
</div>

<h1><?=$forum_title;?></h1>

<table id="topics" cellspacing="1" cellpadding="0" border="0">
<thead>
<tr><th class="t">Topic</th>
	<th class="tp">Posts</th>
	<th class="ta">Author</th>
	<th class="dt">Date</th>
</tr>
</thead>
<tbody>
<?php	
	global $phpbb3_minor_version;
	foreach ($list as $tid) {

		$title = $topics[$tid]['title'];
		$slug = slug($title);
		if($phpbb3_minor_version == 0) {
			$tp = $topics[$tid]['replies'] + 1;
		} elseif ($phpbb3_minor_version == 1 || $phpbb3_minor_version == 2) {
			$tp = $topics[$tid]['replies'];
		} else {
			die('Unknown PHPBB minor version');
		}
		$ta = $topics[$tid]['author'];

		$dt = date('d-m-Y H:i:s', $topics[$tid]['time']);

?>
<tr><td class="t"><a href="<?=$tid;?>/<?= $slug ?>/"><?=$title;?></a></td>
	<td class="tp"><?=$tp;?></td>
	<td class="ta"><?=$ta;?></td>
	<td class="dt"><?=$dt;?></td>
</tr>
<?php

	}  // foreach ($list as $tid)

?>
</tbody>
</table>

<div id="pager" class="pager">
	<form>
		<img src="../img/24-arrow-first.png" class="first"/>
		<img src="../img/24-arrow-previous.png" class="prev"/>
		<input type="text" class="pagedisplay"/>
		<img src="../img/24-arrow-next.png" class="next"/>
		<img src="../img/24-arrow-last.png" class="last"/>
		<select class="pagesize">
			<option selected="selected" value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
			<option value="75">75</option>
			<option value="100">100</option>
		</select>
	</form>
</div>
<?php
include('google_analytics.php');
?>
</body></html>

