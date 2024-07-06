#-----------------------------------------------------------
# Linter
#-----------------------------------------------------------
pint:
	./vendor/bin/pint -v --test

# Fix code directly
pint-hard:
	./vendor/bin/pint -v

lint:
	./vendor/bin/php-cs-fixer fix --diff -v
