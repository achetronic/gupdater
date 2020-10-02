> **DEPRECATED: DDCLIENT NOW SUPPORTS GOOGLE DDNS SO YOU CAN USE IT. TO LEARN HOW TO, LOOK FOR IT [ON MY YOUTUBE CHANNEL](http://youtube.com/achetronic)**


# Gupdater

## Author
I'm Alby Hernández (me@achetronic.com), software developer. Sometimes I deploy things and make 
useful tools, some other times I code. If you find some bug on this project, please, feel free 
and send me an email or fork the code, fix and send a pull request.

## Introduction 
Google DDNS IP updater is a group of scripts (or a docker image) to update IP address of several 
domains/subdomains hosted by DDNS in Google Domains through an API call

## Dependencies
* PHP (version 7)
* php-openssl

## How to install (hard way)
1.  mkdir -p /srv/gupdater
2.  cd /srv/gupdater
3.  download the repository and put the content into "gupdater" directory 
4.  nano /srv/gupdater/gupdater.php
5.  chmod +x /srv/gupdater/gupdater.sh
6.  sh ./srv/gupdater/gupdater.sh

## How to install (easy way with docker)
1.  docker pull achetronic/gupdater:latest
2.  docker run -it achetronic/gupdater:latest -v ./credentials.json:/srv/gupdater/credentials/credentials.json

## credentials.json file
As you can see, there is a file called credentials.json that is mounted into the image. You need a file with your domains with the following structure. Of course you can add as much domains and subdomains as you want. **"*"** character is allowed too
```
[
    {
        "user"   : "your-ddns-user",
        "pass"   : "your-ddns-password",
        "domain" : "your.domain.com"
    },
    {
        "user"   : "another-ddns-user",
        "pass"   : "another-ddns-password",
        "domain" : "*.domain.com"
    }
]

```
