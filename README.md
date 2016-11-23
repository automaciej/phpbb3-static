# phpbb3-static

## Requirements

1. php5-cli
2. php5-mysql
3. PEAR's HTML\_BBCodeParser (http://pear.php.net/package/HTML_BBCodeParser2)

## Usage

1. Edit config.php. Set your database configuration

2. Create static/ directory

        $ mkdir static

3. Run convert.php script

        $ php convert.php

4. Copy resources (css, js, etc)

        $ cp -r templates/res/* static/

5. Point your browser to static/ directory

6. That's all :)
