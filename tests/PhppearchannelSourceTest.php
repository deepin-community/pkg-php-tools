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
 * This class tests \Pkgtools\Phppearchannel\Source
 *
 * @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
 * @author Mathieu Parent <sathieu@debian.org>
 * @license Expat http://www.jclark.com/xml/copying.txt
 */
class PhppearchannelSourceTest extends PHPUnit\Framework\TestCase {
    public function testNoChannelXml() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('channel.xml not found');
        $this->expectOutputString('');
        $pearchan = new \Pkgtools\Phppearchannel\Source('testsuite/data/pearpkg1');
    }

    public function testGetUnknownProperty() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unknown property: \'unknown\'');
        $this->expectOutputString('');
        $pearchan = new \Pkgtools\Phppearchannel\Source('testsuite/data/pearchan1');
        $pearchan->unknown;
    }
}
