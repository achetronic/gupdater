# Gupdater

## Introduction 
Google DDNS IP updater is a group of scripts to update an IP of a domain/subdomain hosted by DDNS in Google Domains through an API call

## How to install
1.  mkdir /srv
2.  mkdir /srv/gupdater
3.  cd /srv/gupdater
4.  download the repository and put the content into "gupdater" directory 
5.  cp /srv/gupdater/gupdater.service /etc/systemd/system/gupdater.service
6.  systemctl enable gupdater
7.  nano /srv/gupdater/gupdater.php
8.  chmod +x /srv/gupdater/gupdater.sh
9.  systemctl start gupdater