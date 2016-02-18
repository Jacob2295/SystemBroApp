# Use phusion/baseimage as base image. To make your builds reproducible, make
# sure you lock down to a specific version, not to `latest`!
# See https://github.com/phusion/baseimage-docker/blob/master/Changelog.md for
# a list of version numbers.
FROM phusion/baseimage:0.9.18

# Use baseimage-docker's init system.
CMD ["/sbin/my_init"]

RUN sudo apt-get update

RUN sudo apt-get install git apache2 php5 libapache2-mod-php5 php5-mcrypt php5-curl php5-gd php5-dev php-pear -y
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN rm -f /var/www/html/*
RUN git clone https://github.com/jwdeitch/SystemBroApp.git /var/www/html
RUN cd /var/www/html/ && composer install --prefer-source --no-interaction
RUN sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
RUN echo "deb http://repo.mongodb.org/apt/ubuntu "$(lsb_release -sc)"/mongodb-org/3.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.0.list
RUN sudo apt-get update
RUN sudo apt-get install -y mongodb-org
RUN printf "\n" | sudo pecl install mongo
RUN echo "extension=mongo.so" >> /etc/php5/apache2/php.ini
RUN sudo chmod -R 0777 /var/www/html/storage/
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' /etc/apache2/sites-enabled/000-default.conf
RUN mkdir -p /data/db

ADD services.sh /usr/sbin/services.sh
RUN chmod +x /usr/sbin/services.sh
CMD ["/usr/sbin/services.sh"]

# Expose port 27017 from the container to the host
EXPOSE 80 27017

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*