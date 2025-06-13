# SPDX-FileCopyrightText: 2025 Julien Lambé <julien@themosis.com>
#
# SPDX-License-Identifier: CC0-1.0

SHELL=/bin/sh

COMMAND=php --version

OCI_IMAGE=php-dev
OCI_ENV=

RUN=podman run -it --rm $(OCI_ENV) -v "$$PWD":/filesystem -w /filesystem $(OCI_IMAGE)

.PHONY: analyze build-oci coverage install* php play test update*

build-oci:
	podman build -t $(OCI_IMAGE) .

install-phpunit:
	$(RUN) composer --working-dir=tools/phpunit install

update-phpunit:
	$(RUN) composer --working-dir=tools/phpunit update

install-phpstan:
	$(RUN) composer --working-dir=tools/phpstan install

update-phpstan:
	$(RUN) composer --working-dir=tools/phpstan update

install-phpcs:
	$(RUN) composer --working-dir=tools/phpcs install

update-phpcs:
	$(RUN) composer --working-dir=tools/phpcs update

install: install-phpunit install-phpstan install-phpcs

update: update-phpunit update-phpstan update-phpcs

reuse:
	podman run --rm -v "$$PWD":/data fsfe/reuse $(COMMAND)

reuse-lint:
	podman run --rm -v "$$PWD":/data fsfe/reuse lint

test:
	$(RUN) php tools/phpunit/vendor/bin/phpunit --configuration phpunit.xml --testdox

coverage: OCI_ENV=--env "XDEBUG_MODE=coverage"
coverage:
	$(RUN) php tools/phpunit/vendor/bin/phpunit --configuration phpunit.xml --coverage-html coverage/html

analyze:
	$(RUN) php tools/phpstan/vendor/bin/phpstan analyze -v -c phpstan.neon src

fix:
	$(RUN) php tools/phpcs/vendor/bin/phpcbf

sniff:
	$(RUN) php tools/phpcs/vendor/bin/phpcs

php:
	$(RUN) $(COMMAND)

