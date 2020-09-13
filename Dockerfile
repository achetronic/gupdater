FROM debian:buster-slim



#### DEFINING VARS
ARG php_version=7.3
ARG minutes_between_requests=10



#### LARAVEL OPERATIONS
RUN apt-get update

# Installing system packages
RUN apt-get install -y -qq --force-yes \
    lsb-base \
    php${php_version}-cli \
	openssl \
	ca-certificates \
    --no-install-recommends > /dev/null

# Installing packages for the script
RUN apt-get install -y -qq --force-yes \
    php${php_version}-json \
	cron \
	--no-install-recommends > /dev/null



####
# Create needed folders
RUN mkdir -p /srv/gupdater

# Download the entire project into the container
COPY . /srv/gupdater/

# Cleaning the system
RUN apt-get -y -qq --force-yes autoremove > /dev/null

# Changing permissions of the entire Laravel
RUN chown root:root -R /srv
RUN find /srv -type f -exec chmod 644 {} \;
RUN find /srv -type d -exec chmod 755 {} \;



#### FINAL OPERATIONS
RUN rm -rf /init.sh && touch /init.sh
RUN echo "#!/bin/bash" >> /init.sh
RUN echo "shopt -s dotglob" >> /init.sh
RUN echo "chmod +x /srv/gupdater/gupdater.sh" >> /init.sh
RUN echo "(crontab -l; echo '" ${minutes_between_requests} " * * * * sh /srv/gupdater/gupdater.sh >> /dev/null 2>&1';) | crontab -" >> /init.sh
RUN echo "/bin/bash" >> /init.sh
RUN chown root:root /init.sh
RUN chmod +x /init.sh
CMD /init.sh

