PHP = $(shell which php) -dphar.readonly=0
COMPOSER = dev/composer.phar
BIN = vendor/bin

install:
	cd dev && wget -O - https://getcomposer.org/installer | $(PHP)

vendor: Makefile
	$(PHP) $(COMPOSER) install && $(PHP) $(COMPOSER) update

fmt:
	$(PHP) $(BIN)/php-cs-fixer fix src

analyse:
	$(PHP) $(BIN)/phpstan analyse -c phpstan.neon.dist
