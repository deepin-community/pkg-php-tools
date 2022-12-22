#!/usr/bin/perl

use strict;
use warnings;
use Cwd;
use File::Temp qw/ tempdir /;
use File::Find ();
use Debian::Debhelper::Buildsystem::phppear;

# Config
my $testpkg = 'testsuite/data/pearpkg1';
my $testpecl = 'testsuite/data/peclpkg1';
my $testchan = 'testsuite/data/pearchan1';

# Init
my $sourcedir = getcwd();
my $failures = 0;
my $destdir;
chdir($testpkg);
my $phppear;

# Step by step ...
# =============================================================================
# CONFIGURE pearpkg1
# =============================================================================
$phppear = Debian::Debhelper::Buildsystem::phppear->new();
$phppear->testmode($sourcedir);
$phppear->pre_building_step();
$phppear->configure();
if (!-f "MyProject_Packages-1.2.1/package.xml") {
	print "FAIL: File does not exists: MyProject_Packages-1.2.1/package.xml\n";
	$failures++;
}

# =============================================================================
# BUILD pearpkg1
# =============================================================================
$phppear = Debian::Debhelper::Buildsystem::phppear->new();
$phppear->testmode($sourcedir);
$phppear->pre_building_step();
$phppear->build();

# =============================================================================
# INSTALL pearpkg1
# =============================================================================
$destdir = tempdir( CLEANUP => 1 );
$phppear = Debian::Debhelper::Buildsystem::phppear->new();
$phppear->testmode($sourcedir);
$phppear->pre_building_step();
$phppear->install($destdir);
if (!-f "$destdir/usr/share/doc/php-foo/package.xml") {
	print "FAIL: File does not exists: \$destdir/usr/share/doc/php-foo/package.xml\n";
	$failures++;
}
if (!-f "$destdir/usr/share/php/dev/null/empty.php") {
	print "FAIL: File does not exists: \$destdir/usr/share/php/dev/null/empty.php\n";
	$failures++;
}
if (!-f "$destdir/usr/share/php/.registry/myproject_packages.reg") {
	print "FAIL: File does not exists: \$destdir/usr/share/php/.registry/myproject_packages.reg\n";
	$failures++;
}
if (-f "$destdir/usr/share/php/tests/MyProject_Packages/empty_test.php") {
	print "FAIL: File does exists: \$destdir/usr/share/php/tests/MyProject_Packages/empty_test.php\n";
	$failures++;
}

my $file_count = 0;
sub process_file {
    my ($dev,$ino,$mode,$nlink,$uid,$gid);

    $file_count++;
}
File::Find::find({wanted => \&process_file}, $destdir);

if ($file_count != 13) {
	print "FAIL: Wrong number of installed files: expected 13, got $file_count\n";
	$failures++;
}

# =============================================================================
# CLEAN pearpkg1
# =============================================================================
$phppear = Debian::Debhelper::Buildsystem::phppear->new();
$phppear->testmode($sourcedir);
$phppear->pre_building_step();
$phppear->clean();
if (-f "MyProject_Packages-1.2.1/package.xml") {
	print "FAIL: File should not exists: MyProject_Packages-1.2.1/package.xml\n";
	$failures++;
}

# =============================================================================
exit($failures != 0);

