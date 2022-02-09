PHP = $(shell which php) -dphar.readonly=0
COMPOSER = dev/composer.phar
BIN = vendor/bin

update:
	$(PHP) $(COMPOSER) update
fmt:
	$(PHP) $(BIN)/php-cs-fixer fix src

analyse:
	$(PHP) $(BIN)/phpstan analyse -c phpstan.neon.dist
