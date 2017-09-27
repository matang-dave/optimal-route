FROM quay.io/hellofresh/php70

# Adds nginx configurations
ADD ./docker/nginx/default.conf   /etc/nginx/sites-available/default

# Environment variables to PHP-FPM
RUN sed -i -e "s/;clear_env\s*=\s*no/clear_env = no/g" /etc/php/7.0/fpm/pool.d/www.conf

# Set apps home directory.
ENV APP_DIR /server/http

# Adds the application code to the image
ADD . ${APP_DIR}


RUN (crontab -l ; echo "* * * * * php /server/http/src/artisan schedule:run >> /dev/null 2>&1") | crontab
 
# Define current working directory.
WORKDIR ${APP_DIR}

# install mbstring
RUN	apt-get install php7.0-mbstring

# install redis
RUN pecl install mongodb


RUN echo "extension=mongodb.so" > /etc/php/7.0/fpm/conf.d/20-mongodb.ini && \
	echo "extension=mongodb.so" > /etc/php/7.0/cli/conf.d/20-mongodb.ini && \
	echo "extension=mongodb.so" > /etc/php/7.0/mods-available/mongodb.ini
	
# Cleanup
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


EXPOSE 80
