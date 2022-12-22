#!/bin/sh

set -e
. ./testsuite/common.sh

testpkg='testsuite/data/pearpkg1'

expect_equal "_pkgtools --sourcedirectory '${testpkg}' phppear changelog" 'Version 1.2.1 - 2009-05-08 (alpha)
----------------------------------------
Notes:
  <none>

Version 0.0.1 - 2009-05-08 (alpha)
----------------------------------------
Notes:
  <none>'
