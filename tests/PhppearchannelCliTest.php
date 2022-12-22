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
 * This class tests basic functionnality of pearchannel command-line
 *
 * @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
 * @author Mathieu Parent <sathieu@debian.org>
 * @license Expat http://www.jclark.com/xml/copying.txt
 */
class PhppearchannelCliTest extends PHPUnit\Framework\TestCase {
    protected $_cli;

    protected function setUp(): void {
        $this->_cli = new \Pkgtools\Command();
    }

    public function testPhppearchannelName() {
        $this->expectOutputString('pear.horde.org');
        chdir('testsuite/data/pearchan1');
        $this->_cli->parseArgs(Array('phppearchannel', 'name'));
    }

    public function testPhppearchannelSummary() {
        $this->expectOutputString('Horde PEAR server');
        chdir('testsuite/data/pearchan1');
        $this->_cli->parseArgs(Array('phppearchannel', 'summary'));
    }

    public function testPhppearchannelSuggestedalias() {
        $this->expectOutputString('horde');
        chdir('testsuite/data/pearchan1');
        $this->_cli->parseArgs(Array('phppearchannel', 'suggestedalias'));
    }

    public function testPhppearchannelSubstvars() {
        $this->expectOutputString('phppear:channel-name=pear.horde.org
phppear:channel-summary=Horde PEAR server
phppear:channel-alias=horde
phppear:channel-common-description=This is the PEAR channel registry entry for horde.${Newline}${Newline}PEAR is a framework and distribution system for reusable PHP components. A${Newline}PEAR channel is a website that provides package for download and a few extra${Newline}meta-information for files.
');
        chdir('testsuite/data/pearchan1');
        $this->_cli->parseArgs(Array('phppearchannel', 'substvars'));
    }
}
