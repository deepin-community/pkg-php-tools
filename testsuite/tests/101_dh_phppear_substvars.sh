#!/bin/sh

set -e
. ./testsuite/common.sh

testpkg='testsuite/data/pearpkg1'
testpecl='testsuite/data/peclpkg1'
testchan='testsuite/data/pearchan1'

sed -e "s@/usr/bin/pkgtools@php', '-d', 'include_path=${PWD}/share/php', '${PWD}/bin/pkgtools@" \
    bin/dh_phppear > bin/dh_phppear.test

ret=0
old_dir="${PWD}"

cd "${testpkg}"
expect_equal "perl '${old_dir}/bin/dh_phppear.test'" '' || ret=$?
expect_equal "cat 'debian/php-foo.substvars'" 'phppear:Debian-Depends=php-common, php-alpha (>= 1.0~a), php-alpha (<= 2.0~alpha3), php-beta (>= 1.0~b2), php-beta (<< 2.0~beta4), php-rc (>= 1.0~RC2), php-rc (<= 2.0~RC5), php-horde-alarm (>= 2.2.0), php-horde-alarm (<< 3.0.0~alpha1), phpunit (>= 1.1), phpunit (<= 1.9)
phppear:Debian-Recommends=php-mysql
phppear:summary=this is a wrapper package for installing dependencies for MyProject
phppear:description=<Some random notes>${Newline}This is a paragraph.${Newline}${Newline}Those are bullets:${Newline} * Number one${Newline} * Number two${Newline}
phppear:channel=pear.php.net' || ret=$?
rm -f debian/php-foo.substvars debian/php-foo.debhelper.log
cd "${old_dir}"

cd "${testpecl}"
expect_equal "perl '${old_dir}/bin/dh_phppear.test'" '' || ret=$?
expect_equal "cat 'debian/php-foo.substvars'" "phppear:Debian-Depends=php-common, phpapi-$(php-config --phpapi)
phppear:summary=this is a wrapper package for installing dependencies for MyProject
phppear:description=<Some random notes>
phppear:channel=pecl.php.net" || ret=$?
rm -f debian/php-foo.substvars debian/php-foo.debhelper.log
cd "${old_dir}"

cd "${testchan}"
expect_equal "perl '${old_dir}/bin/dh_phppear.test'" '' || ret=$?
expect_equal "cat 'debian/php-foo.substvars'" 'phppear:channel-name=pear.horde.org
phppear:channel-summary=Horde PEAR server
phppear:channel-alias=horde
phppear:channel-common-description=This is the PEAR channel registry entry for horde.${Newline}${Newline}PEAR is a framework and distribution system for reusable PHP components. A${Newline}PEAR channel is a website that provides package for download and a few extra${Newline}meta-information for files.' || ret=$?
rm -f debian/php-foo.substvars debian/php-foo.debhelper.log
cd "${old_dir}"

rm bin/dh_phppear.test

exit $ret
