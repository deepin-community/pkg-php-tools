#!/usr/bin/perl
# debhelper sequence file for PHP PEAR

use warnings;
use strict;
use Debian::Debhelper::Dh_Lib;

insert_before("dh_link", "dh_phppear");

1

