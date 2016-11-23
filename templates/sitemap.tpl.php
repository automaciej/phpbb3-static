<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php

global $archive_base_url;

foreach ($urlset as $entry) {
	$loc = $entry['loc'];
	$lastmod = $entry['lastmod'];
?>
<url>
<loc><?= $loc ?></loc>
<lastmod><?= $lastmod ?></lastmod>
</url>
<?php
}  // foreach ($urlset as $entry)
?>
</urlset>
