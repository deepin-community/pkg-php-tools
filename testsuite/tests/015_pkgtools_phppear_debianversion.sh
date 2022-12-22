#!/bin/sh

set -e
. ./testsuite/common.sh

testpkg='testsuite/data/pearpkg1'

ret=0
expect_equal "_pkgtools phppear debianversion '1.0.1'" '1.0.1' || ret=$?
expect_equal "_pkgtools phppear debianversion '2alpha2'" '2~alpha2' || ret=$?
expect_equal "_pkgtools phppear debianversion '2a'" '2~a' || ret=$?
expect_equal "_pkgtools phppear debianversion '0.8beta'" '0.8~beta' || ret=$?
expect_equal "_pkgtools phppear debianversion '0.8b1'" '0.8~b1' || ret=$?
expect_equal "_pkgtools phppear debianversion '1.0rc1'" '1.0~rc1' || ret=$?

exit $ret
