#!/usr/bin/zsh
cd /srv/http
git stash
git pull
git stash pop
