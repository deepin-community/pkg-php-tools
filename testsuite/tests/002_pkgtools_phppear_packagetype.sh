#!/bin/sh

set -e
. ./testsuite/common.sh

testpkg='testsuite/data/pearpkg1'
testpecl='testsuite/data/peclpkg1'

ret=0

expect_equal "_pkgtools --sourcedirectory '${testpkg}' phppear packagetype" 'php' || ret=$?
expect_equal "_pkgtools --sourcedirectory '${testpecl}' phppear packagetype" 'extsrc' | ret=$?

exit $ret
