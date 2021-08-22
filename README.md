# phpbb3-static

## Requirements

1. php7.0-cli
1. php7.0-intl
1. php7.0-mysql (the converter only works with MySQL databases)
1. php7.0-dba
1. PEAR's HTML\_BBCodeParser (https://pear.php.net/package/HTML_BBCodeParser2/)
1. A running instance of your forum (read-only is fine)

## Usage

1. In your forum, go to the control panel, server load section, and set the
   server load limit to zero, and session limit to zero.

   Otherwise the script won't manage to fetch all posts via HTTP, because it
   will be blocked by the forum. If this happens, the script will stop with an
   error.

1. Make sure your forum uses the prosilver skin.

1. Copy config.php-example to config.php and edit it. Set your database
   configuration.

1. Create the static/ directory

        $ mkdir static

1. Run the scripts

        $ php extract.php

   You now have the forum-data.json file in the working directory.

        $ php legacy_write_html.php

1. Copy resources (css, js, etc)

        $ cp -r templates/res/* static/

1. Point your browser to static/ directory

1. Final touches: you might need to copy your forum's smilies directory
   (images/smilies) into the static directory.

1. That's all :)

### Redirects from old URLs

If you would like to preserve the old URLs and redirect to the archive, you can
generate a file with redirection data:

```
php generate_redirection_data.php
```

This command will generate redirection-data.php.

Then customize the included viewforum.php and viewtopic.php files, copy
redirection-data.php there too, it's necessary for redirections to work.

## Bugs / known issues

*   Usability issues: if you go from the list of topics to a topic and then go
    back, you'll lose your position in the list of forums, see issue
    [#8](https://github.com/automatthias/phpbb3-static/issues/8).
*   The code doesn't work with PHP5. It shouldn't be a difficult fix, but… see
    section below.

## Maintenance of phpbb3-static

I used phpbb3-static in 2016, when I was making an archive of my old phpBB
forum. I've finished this work, and I won't work on phpbb3-static any more.

phpbb3-static is bound to be maintained that way: somebody needs to archive
a forum, they use the tool, make some changes and fixed, then they move on.
I will allow phpbb3-static to sit in my repositories on Github, but I won't
develop the project any more.

If there's a new feature you want, you'll have to implement it yourself, or pay
someone else to do it. If you have any code changes to contribute, please send
me a pull request.
