<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html><head><title><?=$forum_name;?></title>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" type="text/css" href="main.css"/>
</head><body>

<h1><?=$forum_name;?></h1>
<h2><?=$forum_description;?></h2>

<table cellspacing="1" cellpadding="0" border="0">
<tr><th class="t">Forum</th>
	<th class="tt">Topics</th>
	<th class="tc">Posts</th>
</tr>
<?php

foreach ($categories as $cid => $cat) {

	$ctitle = $cat['title'];

?>
<tr>
	<th colspan="3" class="cat"><?=$ctitle;?></th>
</tr>
<?php

	foreach ($cat['forums'] as $fid) {

		$tt = $forums[$fid]['ntopics'];
		$tp = $forums[$fid]['nposts'];
		$title = $forums[$fid]['title'];

?>
<tr>
	<td class="t"><a href="<?=$fid;?>/"><?=$title;?></a></td>
	<td class="tt"><?=$tt;?></td>
	<td class="tp"><?=$tp;?></td>
</tr>
<?php

	}
}

?>
</table>
<?php
include('google_analytics.php');
?>
</body></html>
