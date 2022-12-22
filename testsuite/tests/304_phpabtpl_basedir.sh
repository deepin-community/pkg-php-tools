#!/bin/sh

set -e
. ./testsuite/common.sh

composerpkg='testsuite/data/composerpkg1'

expect_equal "bin/phpabtpl --basedir src '${composerpkg}/composer.json'" "<?php

// Require
require_once 'Proj1/Pack1/autoload.php';
require_once 'Proj1/Pack2/autoload.php';

// Suggest

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
spl_autoload_register(
    function(\$class) {
        static \$classes = null;
        if (\$classes === null) {
            \$classes = array(
                ___CLASSLIST___
            );
        }
        \$cn = strtolower(\$class);
        if (isset(\$classes[\$cn])) {
            require ___BASEDIR___\$classes[\$cn];
        }
    },
    ___EXCEPTION___,
    ___PREPEND___
);
// @codeCoverageIgnoreEnd

// Files
require_once __DIR__.'/Vendor/Project/helpers.php';"
