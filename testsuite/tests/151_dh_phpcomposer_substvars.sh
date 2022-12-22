#!/bin/sh

set -e
. ./testsuite/common.sh

composerpkg1='testsuite/data/composerpkg1'
composerpkg2='testsuite/data/composerpkg2'

sed -e "s@/usr/bin/pkgtools@php', '-d', 'include_path=${PWD}/share/php', '${PWD}/bin/pkgtools@" \
    bin/dh_phpcomposer > bin/dh_phpcomposer.test

ret=0
old_dir="${PWD}"

cd "${composerpkg1}"
expect_equal "perl '${old_dir}/bin/dh_phpcomposer.test'" '' || ret=$?
expect_equal "cat 'debian/php-foo.substvars'" 'phpcomposer:name=vendor/project
phpcomposer:description=Some project, from some vendor, from somewhere. Also with a very long line to${Newline}check for that too ;-)
phpcomposer:Debian-require=php-common, php-common, php-proj1-pack1 (>= 2.0), php-proj1-pack1 (<< 3~~), php-proj1-pack2, php-intl
phpcomposer:Debian-require-dev=overrideme-sub (>= 3.7), overrideme-sub (<< 3.8~~)
phpcomposer:Debian-recommend=php-ocramius-proxy-manager (>= 0.3.1), php-ocramius-proxy-manager (<< 0.4~~)
phpcomposer:Debian-suggest=php-apcu, php-curl
phpcomposer:Debian-provide=php-example-project (>= 1.7), php-example-project (<< 1.8~~), php-curl' || ret=$?
rm -f debian/php-foo.substvars debian/php-foo.debhelper.log
cd "${old_dir}"

cd "${composerpkg2}"
expect_equal "perl '${old_dir}/bin/dh_phpcomposer.test'" '' || ret=$?
expect_equal "cat 'debian/php-foo.substvars'" 'phpcomposer:name=vendor/project2
phpcomposer:description=Some vendor project 2
phpcomposer:Debian-require=php-cli, php-proj1-equal-any, php-proj1-equal-self (= ${binary:Version}), php-proj1-equal-wildcard (>= 2), php-proj1-equal-wildcard (<< 3~~), php-proj1-equal-exact (= 3.1.2), php-proj1-range-ge (>= 1.0), php-proj1-range-ge-lt (>= 1.1), php-proj1-range-ge-lt (<< 1.9~~), php-proj1-range-gt-le (>> 1.1), php-proj1-range-gt-le (<= 1.9), php-proj1-range-no-comma (>= 3.3.4), php-proj1-range-no-comma (<< 4~~), php-proj2-range-no-comma (>> 1.1), php-proj2-range-no-comma (<= 2), php-proj1-tilde (>= 1.2), php-proj1-tilde (<< 2~~), php-proj1-tilde-major (>= 5), php-proj1-tilde-major (<< 6~~), php-proj1-caret (>= 1.2.3), php-proj1-caret (<< 2~~), php-proj1-caret-pre-1.0 (>= 0.1.2), php-proj1-caret-pre-1.0 (<< 0.2~~), php-proj1-unsupported-or, php-proj1-stability-dev (>= 0.3.1), php-proj1-stability-dev (<< 0.4~~), php-proj1-stability-patch (>= 0.4~~patch1), php-proj1-stability-beta (>= 2.3~beta2), php-proj1-stabilityflag-alpha-caps (>= 2.0), php-proj1-stabilityflag-alpha-caps (<< 2.1~~), php-proj1-stabilityflag-beta (>= 1.0), php-proj1-stabilityflag-beta (<< 1.1~~), php-proj1-stabilityflag-dev, php-proj1-alias-dev-master, php-proj1-alias-inline, php-proj1-underscore-caps.dot (>= 2.0)
phpcomposer:Debian-recommend=libcurl3 (>= 7.37), libicu52 (>= 50), libxml2 (>= 2.8), libssl1.0.0 (>= 1.0.1), libpcre3 (>= 8.30), libxslt1.1 (>= 1.1.24)
phpcomposer:Debian-suggest=php-proj1-suggest-unparsable, php-proj1-suggest-versioned (>> 12)
phpcomposer:Debian-provide=php-proj1-provide-versioned (>= 1.7), php-proj1-provide-versioned (<< 1.8~~), php-proj1-provide-any' || ret=$?
rm -f debian/php-foo.substvars debian/php-foo.debhelper.log
cd "${old_dir}"

rm bin/dh_phpcomposer.test

exit $ret
