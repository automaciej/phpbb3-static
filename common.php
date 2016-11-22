<?php

include('HTML/BBCodeParser2.php');
$bbopt = array(
	'filters' => array('Basic', 'Email', 'Extended', 'Images', 'Links', 'Lists')
);
$bb = new HTML_BBCodeParser2($bbopt);

function template_print($var, $template) {
	extract($var);
	include($template);
}

function template_get($var, $template) {
	global $template_dir;

	$template = $template_dir . '/' . $template;

	ob_start();
	template_print($var, $template);
	$res = ob_get_contents();
	ob_end_clean();

	return $res;
}

function write_content($file, $content) {
	global $target_dir;

	$target = $target_dir . '/' . $file;
	$dir = dirname($target);

	if (!is_dir($dir)) {
		mkdir($dir, 0755);
	}

	$f = fopen($target, 'w');
	fputs($f, $content);
	fclose($f);
}

function log_info($str) {
	print($str);
}


