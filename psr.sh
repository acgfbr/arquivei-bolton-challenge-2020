#!/bin/sh
./vendor/squizlabs/php_codesniffer/bin/phpcbf -d memory_limit=2000M app/ --standard=PSR12
./vendor/squizlabs/php_codesniffer/bin/phpcs -d memory_limit=2000M --exclude=Generic.Files.LineLength --ignore=app/Models  app/ --standard=PSR12

