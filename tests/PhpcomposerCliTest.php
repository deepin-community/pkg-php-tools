<?php
/*
 * Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

/**
 * This class tests basic functionnality of composer command-line
 *
 * @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
 * @author Mathieu Parent <sathieu@debian.org>
 * @license Expat http://www.jclark.com/xml/copying.txt
 */
class PhpcomposerCliTest extends PHPUnit\Framework\TestCase {
    protected $_cli;

    protected function setUp(): void {
        $this->_cli = new \Pkgtools\Command();
    }

    public function testPhpcomposerName() {
        $this->expectOutputString('vendor/project');
        chdir('testsuite/data/composerpkg1');
        $this->_cli->parseArgs(Array('phpcomposer', 'name'));
    }

    public function testPhpcomposerDescription() {
        $this->expectOutputString('Some project, from some vendor, from somewhere. Also with a very long line to check for that too ;-).');
        chdir('testsuite/data/composerpkg1');
        $this->_cli->parseArgs(Array('phpcomposer', 'description'));
    }

    public function testPhpcomposerDependencies() {
        $this->expectOutputString('require:/php
require:/php (>= 5.2)
require:proj1/pack1 (>= 2.0, < 3~~)
require:proj1/proj1-pack2
require:pear-pecl.php.net/spl
require:symfony/polyfill-intl-icu (>= 1.0, < 2~~)
require-dev:overrideme/overrideme-sub (>= 3.7, < 3.8~~)
recommend:ocramius/proxy-manager (>= 0.3.1, < 0.4~~)
suggest:pear-pecl.php.net/apc
suggest:pear-pecl.php.net/curl
provide:example/project (>= 1.7, < 1.8~~)
provide:pear-pecl.php.net/curl
');
        chdir('testsuite/data/composerpkg1');
        $this->_cli->parseArgs(Array('phpcomposer', 'dependencies'));
    }

    public function testPhpcomposerSubstvars() {
        $this->expectOutputString('phpcomposer:name=vendor/project
phpcomposer:description=Some project, from some vendor, from somewhere. Also with a very long line to${Newline}check for that too ;-)
phpcomposer:Debian-require=php-common, php-common, php-proj1-pack1 (>= 2.0), php-proj1-pack1 (<< 3~~), php-proj1-pack2, php-intl
phpcomposer:Debian-require-dev=overrideme-sub (>= 3.7), overrideme-sub (<< 3.8~~)
phpcomposer:Debian-recommend=php-ocramius-proxy-manager (>= 0.3.1), php-ocramius-proxy-manager (<< 0.4~~)
phpcomposer:Debian-suggest=php-apcu, php-curl
phpcomposer:Debian-provide=php-example-project (>= 1.7), php-example-project (<< 1.8~~), php-curl
');
        chdir('testsuite/data/composerpkg1');
        $this->_cli->parseArgs(Array('phpcomposer', 'substvars'));
    }

    // composerpkg2
    public function testPhpcomposerDependencies2() {
        $this->expectOutputString('require:/php-cli
require:proj1/equal-any
require:proj1/equal-self (>= self.version, <= self.version)
require:proj1/equal-wildcard (>= 2, < 3~~)
require:proj1/equal-exact (>= 3.1.2, <= 3.1.2)
require:proj1/range-ge (>= 1.0)
require:proj1/range-ge-lt (>= 1.1, < 1.9~~)
require:proj1/range-gt-le (> 1.1, <= 1.9)
require:proj1/range-no-comma (>= 3.3.4, < 4~~)
require:proj2/range-no-comma (> 1.1, <= 2)
require:proj1/tilde (>= 1.2, < 2~~)
require:proj1/tilde-major (>= 5, < 6~~)
require:proj1/caret (>= 1.2.3, < 2~~)
require:proj1/caret-pre-1.0 (>= 0.1.2, < 0.2~~)
require:proj1/unsupported-or
require:proj1/stability-dev (>= 0.3.1, < 0.4~~)
require:proj1/stability-patch (>= 0.4~~patch1)
require:proj1/stability-beta (>= 2.3~beta2)
require:proj1/stabilityflag-alpha-caps (>= 2.0, < 2.1~~)
require:proj1/stabilityflag-beta (>= 1.0, < 1.1~~)
require:proj1/stabilityflag-dev
require:proj1/alias-dev-master
require:proj1/alias-inline
require:proj1/underscore_CAPS.dot (>= 2.0)
recommend:/lib-curl (>= 7.37)
recommend:/lib-iconv
recommend:/lib-icu (>= 50)
recommend:/lib-libxml (>= 2.8)
recommend:/lib-openssl (>= 1.0.1)
recommend:/lib-pcre (>= 8.30)
recommend:/lib-uuid
recommend:/lib-xsl (>= 1.1.24)
recommend:/lib-unknown
suggest:proj1/suggest-unparsable
suggest:proj1/suggest-versioned (> 12)
provide:proj1/provide-versioned (>= 1.7, < 1.8~~)
provide:proj1/provide-any
');
        chdir('testsuite/data/composerpkg2');
        $this->_cli->parseArgs(Array('phpcomposer', 'dependencies'));
    }

    public function testPhpcomposerSubstvars2() {
        $this->expectOutputString('phpcomposer:name=vendor/project2
phpcomposer:description=Some vendor project 2
phpcomposer:Debian-require=php-cli, php-proj1-equal-any, php-proj1-equal-self (= ${binary:Version}), php-proj1-equal-wildcard (>= 2), php-proj1-equal-wildcard (<< 3~~), php-proj1-equal-exact (= 3.1.2), php-proj1-range-ge (>= 1.0), php-proj1-range-ge-lt (>= 1.1), php-proj1-range-ge-lt (<< 1.9~~), php-proj1-range-gt-le (>> 1.1), php-proj1-range-gt-le (<= 1.9), php-proj1-range-no-comma (>= 3.3.4), php-proj1-range-no-comma (<< 4~~), php-proj2-range-no-comma (>> 1.1), php-proj2-range-no-comma (<= 2), php-proj1-tilde (>= 1.2), php-proj1-tilde (<< 2~~), php-proj1-tilde-major (>= 5), php-proj1-tilde-major (<< 6~~), php-proj1-caret (>= 1.2.3), php-proj1-caret (<< 2~~), php-proj1-caret-pre-1.0 (>= 0.1.2), php-proj1-caret-pre-1.0 (<< 0.2~~), php-proj1-unsupported-or, php-proj1-stability-dev (>= 0.3.1), php-proj1-stability-dev (<< 0.4~~), php-proj1-stability-patch (>= 0.4~~patch1), php-proj1-stability-beta (>= 2.3~beta2), php-proj1-stabilityflag-alpha-caps (>= 2.0), php-proj1-stabilityflag-alpha-caps (<< 2.1~~), php-proj1-stabilityflag-beta (>= 1.0), php-proj1-stabilityflag-beta (<< 1.1~~), php-proj1-stabilityflag-dev, php-proj1-alias-dev-master, php-proj1-alias-inline, php-proj1-underscore-caps.dot (>= 2.0)
phpcomposer:Debian-recommend=libcurl3 (>= 7.37), libicu52 (>= 50), libxml2 (>= 2.8), libssl1.0.0 (>= 1.0.1), libpcre3 (>= 8.30), libxslt1.1 (>= 1.1.24)
phpcomposer:Debian-suggest=php-proj1-suggest-unparsable, php-proj1-suggest-versioned (>> 12)
phpcomposer:Debian-provide=php-proj1-provide-versioned (>= 1.7), php-proj1-provide-versioned (<< 1.8~~), php-proj1-provide-any
');
        $this->_cli->parseArgs(Array('--sourcedirectory', 'testsuite/data/composerpkg2', 'phpcomposer', 'substvars'));
    }
}
