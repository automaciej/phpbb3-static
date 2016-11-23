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

	$one_up_dir = dirname($dir);
	if (!is_dir($one_up_dir) && !is_link($one_up_dir)) {
		mkdir($one_up_dir, 0755);
	}

	if (!is_dir($dir) && !is_link($dir)) {
		mkdir($dir, 0755);
	}

	$f = fopen($target, 'w');
	fputs($f, $content);
	fclose($f);
}

function log_info($str) {
	print($str);
}

function slug($str) {
		$str = transliterator_transliterate(
			"Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();",
			$str);
		$str = str_replace(' ', '-', $str);
		$str = str_replace('/', '-', $str);
		$str = str_replace('--', '-', $str);
		$str = trim($str, '-');
		return $str;
}
