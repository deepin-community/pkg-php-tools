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
 * This class tests basic functionnality of \Pkgtools\{Base,\}Command.php
 *
 * @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
 * @author Mathieu Parent <sathieu@debian.org>
 * @license Expat http://www.jclark.com/xml/copying.txt
 */
class BaseCliTest extends PHPUnit\Framework\TestCase {
    protected $_cli;
    protected $_baseHelp = 'Usage:
    pkgtools COMMAND

Options:
    --help: print help
    -h: print help
    --verbose: increase verbosity
    -v: increase verbosity
    --sourcedirectory: set source directory
    -D: set source directory

Commands:
  : Without arguments: print help
  phpcomposer: All Composer related commands
  phppear: All PEAR and PECL related commands
  phppearchannel: All PEAR channel related commands
';

    protected function setUp(): void {
        $this->_cli = new \Pkgtools\Command();
    }

    public function testNoArg() {
        $this->expectOutputString($this->_baseHelp);
        // Fake empty args
        $_SERVER['argv'] = Array();
        $this->_cli->parseArgs();
    }

    public function testHelp() {
        $this->expectOutputString($this->_baseHelp);
        $this->_cli->parseArgs(Array('--help'));
    }

    public function testUnknownOption() {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Unknown option --unknown');
        $this->expectOutputString('');
        $this->_cli->parseArgs(Array('--unknown'));
    }

    public function testUnknownSubcommand() {
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Unknown sub-command unknown');
        $this->expectOutputString('');
        $this->_cli->parseArgs(Array('unknown'));
    }

    public function testLogging() {
        $level0 = \Pkgtools\Base\Logger::getEffectiveLevel();
        $this->assertEquals(30, $level0);

        $this->expectOutputString('pear.php.net');
        chdir('testsuite/data/pearpkg1');
        $this->_cli->parseArgs(Array('-v', '-v', 'phppear', 'channel'));

        $level1 = \Pkgtools\Base\Logger::getEffectiveLevel();
        $this->assertEquals(10, $level1);

        \Pkgtools\Base\Logger::setLevel($level0);
        $level2 = \Pkgtools\Base\Logger::getEffectiveLevel();
        $this->assertEquals(30, $level2);
    }
}
