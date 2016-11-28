# TODO

1. Make $filter_topic usable
2. Corrupted exports: URLs in posts get broken
3. Usability: When pressing "Back" on the topic page, we go back to the top of
   the list of topics, losing the position where we were.

## Proper bbcode parsing

Related threads:

-  Somebody asking how to display a post on another page:
   https://area51.phpbb.com/phpBB/viewtopic.php?t=31746
-  phpBB wiki pages
   -  https://wiki.phpbb.com/Function.generate_text_for_display
      Unfortunately this requires pulling in half of phpBB
   -  https://wiki.phpbb.com/Tutorial.Parsing_text
-  A blog entry about displaying posts and topics on external pages
   https://blog.phpbb.com/2009/11/09/how-to-display-posts-and-topics-on-external-pages/

Use the phpBB parser for parsing bbcode. This requires using their database
and other things.
