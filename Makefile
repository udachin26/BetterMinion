PHP = $(shell which php) -dphar.readonly=0
BIN = vendor/bin

fmt:
	$(PHP) $(BIN)/php-cs-fixer fix src

analyse:
	$(PHP) $(BIN)/phpstan analyse -c phpstan.neon.dist