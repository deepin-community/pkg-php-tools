#!/bin/sh

set -e
. ./testsuite/common.sh

testpkg='testsuite/data/pearpkg1'
testpecl='testsuite/data/peclpkg1'

ret=0
expect_equal '_pkgtools phppear debianname pear.horde.org horde' 'php-horde' || ret=$?
expect_equal '_pkgtools phppear debianname pecl.php.net spl' '' || ret=$?
expect_equal '_pkgtools phppear debianname pecl.php.net SPL' '' || ret=$?
expect_equal '_pkgtools phppear debianname pecl.php.net pcntl' '' || ret=$?
expect_equal '_pkgtools phppear debianname pear.php.net Net_SMTP' 'php-net-smtp' || ret=$?
expect_equal '_pkgtools phppear debianname phpseclib.sourceforge.net File_ASN1' 'php-phpseclib-file-asn1' || ret=$?

expect_equal "_pkgtools --sourcedirectory '${testpkg}' phppear debianname" 'php-myproject-packages' || ret=$?
expect_equal "_pkgtools --sourcedirectory '${testpecl}' phppear debianname" 'php-myproject-packages' || ret=$?

exit $ret
