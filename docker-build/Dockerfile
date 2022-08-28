FROM philippjenni/docker-amp:jammy

MAINTAINER Philipp Jenni <philippjenni@bluemail.ch>

RUN apt-get -y update

ENV MYSQL_ENABLE true

# append own configuration to /etc/mysql/my.cnf
ADD my.cnf /tmp/my.cnf
RUN cat /tmp/my.cnf >> /etc/mysql/my.cnf

# Copy site into place.
ADD www $WWW_ROOT
# Giving rights to user www-data to data dir of application
RUN chown -R www-data $WWW_ROOT


CMD ["/startScripts/start-amp.sh"]