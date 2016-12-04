# Change Log

The format is based on [Keep a Changelog](http://keepachangelog.com/), and
releases follow the [semantic versioning guidelines](http://semver.org/).

## [Unreleased]
### Added
-   viewtopic.php and viewforum.php shims to preserve old URLs and add redirects
-   An extra variable to append HTML to the end of each topic page.
-   Support for Google Analytics

### Changed
-   Fetching post content from a live forum instead of own bbcode parsing.
-   Fetching data and writing HTML has been split into 2 steps.

## [0.2.0] ― 2016-11-30
### Added
-   Sitemap support

### Changed
-   Pretty URLs with a slug: /x/yyy/ is a redirect to /x/yyy/topic-title/

### Fixed
-   `mysql_*` functions to PDO style MySQL access.

## [0.1.0] ― 2007-12-12
## Added
-   Initial import by Fajran Iman Rusadi  <fajran@gmail.com>
