#!/bin/sh

set -e
. ./testsuite/common.sh

testchan='testsuite/data/pearchan1'

expect_equal "_pkgtools --sourcedirectory '${testchan}' phppearchannel summary" 'Horde PEAR server'
