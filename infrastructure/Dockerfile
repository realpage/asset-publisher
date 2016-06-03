FROM realpage/fpm-nginx:7

# Copy the application files to the container
ADD . /var/www/html
ADD infrastructure/nginx /etc/nginx/conf.d

# Get the latest composer
ADD https://getcomposer.org/composer.phar /usr/bin/composer

RUN \
    # fix permissions locally
    #sed -ri 's/^www-data:x:82:82:/www-data:x:1000:1000:/' /etc/passwd \
    #&& sed -ri 's/^www-data:x:82:/www-data:x:50:/' /etc/group \

    # fix permissions in CI
    sed -ri 's/^www-data:x:82:82:/www-data:x:1001:1001:/' /etc/passwd \
    && sed -ri 's/^www-data:x:82:/www-data:x:1001:/' /etc/group \

    # fix permissions in CI
    && apk add --update --no-cache \
        # needed for mcrypt
        libmcrypt \

        # needed for composer
        git zip unzip \

        # needed to clone repos
        openssh \

    # php extensions the app will need
    && docker-php-ext-install mcrypt mbstring pdo_mysql \

    # install composer
    && wget -O /usr/bin/composer https://getcomposer.org/composer.phar \
    && chmod +x /usr/bin/composer \
    && mkdir -p /home/www-data/.composer \

    # allow git checkouts to write to a folder
    && chmod 600 /var/www/html/storage/git \

    # give www-data the keys
    && chown -R www-data:www-data /var/www/html /usr/bin/composer /home/www-data \

    && chmod -R 744 /home/www-data/.composer

WORKDIR /var/www/html

USER www-data

RUN chmod +x infrastructure/start.sh \

    # parallel dependency installation
    && composer global require hirak/prestissimo \

    # production-ready dependencies
    && composer install  --no-interaction --no-dev --prefer-dist

USER root

# Add private key and then start the process monitor
ENTRYPOINT /var/www/html/infrastructure/start.sh;/init