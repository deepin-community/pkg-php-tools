VERSION=$(shell expr "`dpkg-parsechangelog |grep Version:`" : '.*Version: \(.*\)')

POD2MAN=pod2man --utf8 -c Debhelper -r "$(VERSION)"

build:
	$(POD2MAN) bin/dh_phppear dh_phppear.1
	$(POD2MAN) bin/dh_phpcomposer dh_phpcomposer.1
	$(POD2MAN) -c pkg-php-tools bin/phpabtpl phpabtpl.1
	$(POD2MAN) -s 7 doc/pkg-php-tools.pod pkg-php-tools.7

test:
	sh testsuite/runtests.sh

clean:
	if [ -e .phpunit.result.cache ]; then rm .phpunit.result.cache; fi;
	if [ -e dh_phppear.1 ]; then rm dh_phppear.1; fi;
	if [ -e dh_phpcomposer.1 ]; then rm dh_phpcomposer.1; fi;
	if [ -e phpabtpl.1 ]; then rm phpabtpl.1; fi;
	if [ -e pkg-php-tools.7 ]; then rm pkg-php-tools.7; fi;
