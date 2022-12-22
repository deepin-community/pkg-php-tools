#!/bin/sh

if [ ! -f 'debian/control' ]; then
    echo "ERROR: testsuite should run from the source directory"
    exit 1
fi

tests_run=''
tests_failed=''
tests_skipped=''
tests_unknown=''
tests_success=''

for test in testsuite/tests/*.sh; do
    test_name=$(basename "${test}")
    echo "Running test: ${test_name}"
    ret=0
    sh "${test}" || ret=$?
    case "$ret" in
      0) tests_success="${tests_success} ${test_name}"; echo 'SUCCESS';;
      1) tests_failed="${tests_failed} ${test_name}"; echo 'FAILED';;
      2) tests_skipped="${tests_skipped} ${test_name}"; echo 'SKIPPED';;
      *) tests_unknown="${tests_unknown} ${test_name}"; echo 'UNKNOWN';;
    esac
    tests_run="${tests_run} ${test_name}"
done

echo '====================================================================='
echo 'TEST RESULT SUMMARY'
echo '---------------------------------------------------------------------'
echo 'Number of tests :' $(echo "${tests_run}" | wc -w)
echo 'Test passed     :' $(echo "${tests_success}" | wc -w)
echo 'Test failed     :' $(echo "${tests_failed}" | wc -w)
echo 'Test skipped    :' $(echo "${tests_skipped}" | wc -w)
echo '---------------------------------------------------------------------'

if [ $(echo "${tests_failed}" | wc -w) != 0 ]; then
    exit 1
fi
if [ $(echo "${tests_run}" | wc -w) != $(echo "${tests_success}" | wc -w) ]; then
    exit 2
fi
