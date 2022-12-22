#!/bin/sh

set -e
. ./testsuite/common.sh

testpkg='testsuite/data/pearpkg1'

expect_equal "_pkgtools --sourcedirectory '${testpkg}' phppear name" 'MyProject_Packages'
