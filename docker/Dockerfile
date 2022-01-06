FROM yiisoftware/yii2-php:7.4-apache

RUN apt-get update && apt-get install -y sendmail
RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint
RUN service sendmail restart