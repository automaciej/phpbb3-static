# phpbb3-static

## Requirements

1. php7.0-cli
1. php7.0-intl
1. php7.0-mysql (the converter only works with MySQL databases)
1. php7.0-dba
1. PEAR's HTML\_BBCodeParser (http://pear.php.net/package/HTML\_BBCodeParser2)
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

        $ php legacy_generate_html.php

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

There are usability issues: if you go from the list of topics to a topic and then
go back, you'll lose your position in the list of forums, see issue #3.
