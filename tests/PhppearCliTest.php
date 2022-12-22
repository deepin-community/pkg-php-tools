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
 * This class tests basic functionnality of pear command-line
 *
 * @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
 * @author Mathieu Parent <sathieu@debian.org>
 * @license Expat http://www.jclark.com/xml/copying.txt
 */
class PhppearCliTest extends PHPUnit\Framework\TestCase {
    protected $_cli;

    protected function setUp(): void {
        $this->_cli = new \Pkgtools\Command();
    }

    public function testPhppearName() {
        $this->expectOutputString('MyProject_Packages');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'name'));
    }

    public function testPhppearChannel() {
        $this->expectOutputString('pear.php.net');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'channel'));
    }

    public function testPhppearSummary() {
        $this->expectOutputString('this is a wrapper package for installing dependencies for MyProject.');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'summary'));
    }

    public function testPhppearDescription() {
        $this->expectOutputString('<Some random notes>
    This is a paragraph.

    Those are bullets:
    * Number one
    * Number two
 ');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'description'));
    }

    public function testPhppearVersion() {
        $this->expectOutputString('1.2.1');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'version'));
    }

    public function testPhppearPackagetype() {
        $this->expectOutputString('php');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'packagetype'));
    }

    public function testPhppearPackagetypePECL() {
        $this->expectOutputString('extsrc');
        chdir('testsuite/data/peclpkg1');
        $this->_cli->parseArgs(Array('phppear', 'packagetype'));
    }

    public function testPhppearDependencies() {
        $this->expectOutputString('require:/php (>= 5.2.1)
require:pear-pear.php.net/Alpha (>= 1.0~a, <= 2.0~alpha3)
require:pear-pear.php.net/Beta (>= 1.0~b2, < 2.0~beta4)
require:pear-pear.php.net/RC (>= 1.0~RC2, <= 2.0~RC5)
require:pear-pear.horde.org/Horde_Alarm (>= 2.2.0, < 3.0.0~alpha1)
require:pear-pear.phpunit.de/PHPUnit (>= 1.1, <= 1.9)
recommend:pear-pecl.php.net/mysqli
');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'dependencies'));
    }

    public function testPhppearSubstvars() {
        $this->expectOutputString('phppear:Debian-Depends=php-common, php-alpha (>= 1.0~a), php-alpha (<= 2.0~alpha3), php-beta (>= 1.0~b2), php-beta (<< 2.0~beta4), php-rc (>= 1.0~RC2), php-rc (<= 2.0~RC5), php-horde-alarm (>= 2.2.0), php-horde-alarm (<< 3.0.0~alpha1), phpunit (>= 1.1), phpunit (<= 1.9)
phppear:Debian-Recommends=php-mysql
phppear:Debian-Suggests=
phppear:Debian-Breaks=
phppear:summary=this is a wrapper package for installing dependencies for MyProject
phppear:description=<Some random notes>${Newline}This is a paragraph.${Newline}${Newline}Those are bullets:${Newline} * Number one${Newline} * Number two${Newline}
phppear:channel=pear.php.net
');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('phppear', 'substvars'));
    }
}
