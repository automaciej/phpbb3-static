# phpbb3-static

## Requirements

1. php7.0-cli
1. php7.0-intl
1. php7.0-mysql (the converter only works with MySQL databases)
1. php7.0-dba
1. PEAR's HTML\_BBCodeParser (https://pear.php.net/package/HTML_BBCodeParser2/)
   
   These commands worked for me:

        $ sudo pear channel-update pear.php.net
        $ sudo pear install HTML_BBCodeParser2-0.1.0
		$ sudo chmod -R o+rX /usr/share/php/HTML

1. A running instance of your forum (read-only is fine).

## Usage

1. In your forum, go to the control panel, server load section, and set the
   server load limit to zero, and session limit to zero.

   Otherwise the script won't manage to fetch all posts via HTTP, because it
   will be blocked by the forum. If this happens, the script will stop with an
   error.

1. Also in the forum administration, ensure that every forum sits in a top-level
   category forum. If there are top level forums, create a category forum "Forums"
   and set the parent of all top level forums to this category.

   The archive script currently only works correctly for forums which are a child
   of a category forum.

1. Make sure your forum uses the `prosilver` skin.

1. Clone this repository to an arbitrary location (not to the forum root!) and cd into it.

1. Copy `config.php-example` to `config.php`:

        $ cp config.php-example config.php

   Then, edit `config.php` and set your database configuration.

1. Run the scripts

        $ php extract.php

   You now have the `forum-data.json` file in the working directory.
   
   The script also creates `forum-data_download_cache.dbm` which is a cache
   of the downloaded pages. Re-running `extract.php` will not re-download the
   pages.

1. Extract HTML pages to the `static/` directory and copy scripts and smilies:

        $ php legacy_write_html.php

1. [optional] Redirects from old URLs

   If you would like to preserve the old URLs and redirect to the archive,
   generate the file with redirection data:

        $ php generate_redirection_data.php

   The script will create `redirection-data.php` in the `static/` directory.
   It will also copy `viewforum.php` and `viewtopic.php` there.
   
   If your old forum URL is different from the archive, you will need to copy these
   _three_ files into the root of your old forum URL.

1. Point your browser to `static/` directory

1. That's all :)


## Bugs / known issues

*   Cannot extract top-level forums with topics.
    Workaround: create a top level category forum and set the parent of all top
	level forums to that category forum.
*   Usability issues: if you go from the list of topics to a topic and then go
    back, you'll lose your position in the list of forums, see issue
    [#8](https://github.com/automatthias/phpbb3-static/issues/8).
*   The code doesn't work with PHP5.

## Maintenance of phpbb3-static

We at bome have extended and used these scripts in August 2020 to archive
forums running PHPBB 3.3.

From previous maintainer automatthias:
I used phpbb3-static in 2016, when I was making an archive of my old phpBB
forum. I've finished this work, and I won't work on phpbb3-static any more.

phpbb3-static is bound to be maintained that way: somebody needs to archive
a forum, they use the tool, make some changes and fixed, then they move on.
I will allow phpbb3-static to sit in my repositories on Github, but I won't
develop the project any more.

If there's a new feature you want, you'll have to implement it yourself, or pay
someone else to do it. If you have any code changes to contribute, please send
me a pull request.
