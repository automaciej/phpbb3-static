#!/usr/bin/env python
"""Fetch a HTML snippet from a phpBB forum and save it on disk.

This is a hacky way of doing it.
"""

import argparse
import logging
import requests
from bs4 import BeautifulSoup

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('forum_url', help='Base URL of the forum')
    parser.add_argument('post_id', help='Post number')
    parser.add_argument('target_file', help='Write to file')
    parser.add_argument('--debug', help='Enable debug logging',
            default=False, action='store_true')
    args = parser.parse_args()
    log_level = logging.WARNING
    if args.debug:
        log_level = logging.DEBUG
    logging.basicConfig(level=log_level)
    url = args.forum_url + ('/viewtopic.php?p=%s' % args.post_id)
    logging.info('getting %s', url)
    resp = requests.get(url)
    soup = BeautifulSoup(resp.text, 'html.parser')
    post_id_in_html = 'p%s' % args.post_id
    with open(args.target_file, 'w') as fd:
        post = soup.find('div', attrs={'id': post_id_in_html})
        if post is not None:
            div = post.find('div', attrs={'class': 'content'}).encode()
            fd.write(div.decode('utf-8'))


if __name__ == '__main__':
    main()
