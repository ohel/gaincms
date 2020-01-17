#!/bin/sh

# Copyright 2020 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# A convenience script to synchronize files to a web host via SSH, fixing permissions.

ssh_host=ssh.gainit.fi
web_host=www.gainit.fi

echo "Synchronizing files..."
rsync -a -e ssh --progress index.php .htaccess sitemap.xml $ssh_host:~/public_html/
# You may use a local host in site config as it will be changed to the real one here.
ssh $ssh_host 'sed -i "s/\(define(\"CONFIG_URL_BASE\", \"\)[^\"]*\"/\1https:\/\/'$web_host'\"/" public_html/index.php'
rsync -a -e ssh --delete --progress ./site/* $ssh_host:~/public_html/site

echo "Fixing permissions..."
ssh $ssh_host 'cd ~/public_html/site; . permissions'

echo "Done."
