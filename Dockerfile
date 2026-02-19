FROM dunglas/frankenphp

RUN apt-get update \
	&& apt-get install -y --no-install-recommends git unzip \
	&& rm -rf /var/lib/apt/lists/*
RUN install-php-extensions pdo_mysql zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
