# phpbb3-static

## Requirements

1. php7.0-cli
2. php7.0-mysql
3. php7.0-intl
4. PEAR's HTML\_BBCodeParser (http://pear.php.net/package/HTML_BBCodeParser2)
5. Python + requests + Beautiful Soup 4

## Usage

1. Copy config.php-example to config.php and edit it. Set your database
   configuration.

2. Create static/ directory

        $ mkdir static

3. Run convert.php script

        $ php convert.php

4. Copy resources (css, js, etc)

        $ cp -r templates/res/* static/

5. Point your browser to static/ directory

6. That's all :)

## Bugs / known issues

There are usability issue: if you go from the list of topics to a topic and then
go back, you'll lose your position in the list of forums, see issue #3.
