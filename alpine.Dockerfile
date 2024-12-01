FROM alpine:edge

RUN apk add --no-cache php83 php83-pecl-apcu php83-pdo_mysql php83-pdo_sqlite php83-curl

RUN alias php=php83
RUN mkdir -p /app/public/
RUN mkdir -p /vcard/public
RUN ln -s /vcard/public  /app/public/q
WORKDIR /app
ENV PHP_CLI_SERVER_WORKERS=4
CMD ["php83", "-S", "0.0.0.0:8081","-t", "./public" ]
#or
# CMD ["php", "-S", "0.0.0.0:8802","./router.php" ]