FROM debian:buster



#### DEFINING VARS
ARG php_version=7.3
ARG minutes_between_requests=10



#### LARAVEL OPERATIONS
RUN apt-get update

# Installing system packages
RUN apt-get install -y -qq --force-yes lsb-base php${php_version}-cli openssl ca-certificates --no-install-recommends > /dev/null

# Installing packages for the script
RUN apt-get install -y -qq --force-yes php${php_version}-json cron nano --no-install-recommends > /dev/null



####
# Create needed folders
RUN mkdir -p /srv/gupdater

# Download the entire project into the container
COPY . /srv/gupdater/

# Cleaning the system
RUN apt-get -y -qq --force-yes autoremove > /dev/null

# Changing permissions of the program
RUN chown root:root -R /srv
RUN find /srv -type f -exec chmod 644 {} \;
RUN find /srv -type d -exec chmod 755 {} \;
RUN chmod +x /srv/gupdater/gupdater.sh

# CONFIGURE CRON Schedule the renovation (set to every minute)
RUN touch /var/spool/cron/crontabs/root 
RUN echo "* * * * * /srv/gupdater/gupdater.sh" >> /var/spool/cron/crontabs/root



#### OPERATIONS
# ENTRYPOINT
RUN rm -rf /entrypoint.sh && touch /entrypoint.sh
RUN echo "#!/bin/bash" >> /entrypoint.sh
RUN echo "service cron start" >> /entrypoint.sh
RUN echo "(crontab -l; echo '${minutes_between_requests} * * * * /srv/gupdater/gupdater.sh >> /dev/null 2>&1';) | crontab -" >> /entrypoint.sh
RUN echo "touch /etc/crontab /etc/cron.*/*" >> /entrypoint.sh
RUN echo 'exec "$@"' >> /entrypoint.sh

RUN chown root:root /entrypoint.sh
RUN chmod +x /entrypoint.sh

# CMD
RUN rm -rf /init.sh && touch /init.sh
RUN echo "#!/bin/bash" >> /init.sh
RUN echo "/srv/gupdater/gupdater.sh" >> /init.sh
RUN echo "/bin/bash" >> /init.sh

RUN chown root:root /init.sh
RUN chmod +x /init.sh

# GAINING COMFORT
WORKDIR "/srv/gupdater"

# EXECUTING START SCRIPT
ENTRYPOINT ["/entrypoint.sh"]
CMD /init.sh

