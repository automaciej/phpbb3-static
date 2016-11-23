<?php

global $bb;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title><?=$title;?> - <?=$forum_name;?></title>
<link rel="stylesheet" type="text/css" href="../../../topic.css"/>
</head><body>

<div class="breadcrumb">
	<p><a href="../../../">Forums Archive Home</a> &raquo; <a href="../../"><?=$forum_title;?></a></p>
</div>

<h1><?=$title;?></h1>

<div class="original"><p>Live forum: 
	<a href="<?=$url;?>"><?=$url;?></a>
</p></div>

<?php

	foreach ($posts as $post) {

		$user = $post['username'];
		$text = $post['post_text'];
		$time = $post['post_time'];
		$dt = date('d-m-Y H:i:s', $time);
		$bid = $post['bbcode_uid'];
		$post_id = $post['post_id'];

		$text = str_replace(':' . $bid, '', $text);
		$text = preg_replace('/\[(\/?)code:\d*\]/', '[\1code]', $text);
		$html = nl2br($bb->qParse($text));

?>
<div class="post" id="p<?=$post_id?>">
	<div class="info">
		<p class="poster"><?=$user;?></p>
		<p class="dt"><?=$dt;?></p>
	</div>
	<div class="msg">
	<!-- BEGIN MESSAGE -->
	<?=$html;?>
	<!-- END MESSAGE -->
	</div>
</div>
<?php

	}

?>
</body></html>

