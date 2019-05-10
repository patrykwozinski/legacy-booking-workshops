FROM docplanner/php:7.3-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && docker-php-ext-enable xdebug

COPY docker/development/nginx.conf /etc/nginx/conf.d/default.conf

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/composer

COPY --chown=dp ./docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

USER dp
RUN composer global require hirak/prestissimo

WORKDIR /var/www/html
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint"]
