# TODO

1. Usability: When using pagination, pressing "Back" on the topic page will go back to the top of
   the list of topics, losing the position where we were.

1. Usability: automatically get DB config from `$forum_dir . "/config.php"`

1. Compatibility: cannot extract topics from a top-level forum.
   All forums with topics must have a category parent forum.

1. Compatibility: make it work with PHP 7.4

1. Compatibility: allow setting PHPBB minor version to 3.3 (`$phpbb3_minor_version`)

1. Usability: automatically get `$phpbb3_minor_version` from `$forum_dir`
