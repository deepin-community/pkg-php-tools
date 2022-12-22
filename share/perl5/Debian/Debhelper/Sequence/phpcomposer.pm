#!/usr/bin/perl
# debhelper sequence file for PHP Composer

use warnings;
use strict;
use Debian::Debhelper::Dh_Lib;

insert_before("dh_link", "dh_phpcomposer");

1

