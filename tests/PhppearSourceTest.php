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

//namespace Pkgtools;

/**
* This class tests basic functionnality of \Pkgtools\Phppear\Source
*
* @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
* @author Mathieu Parent <sathieu@debian.org>
* @license Expat http://www.jclark.com/xml/copying.txt
*/
class PhppearSourceTest extends PHPUnit\Framework\TestCase {
    public function testFailedOpen() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('package.xml not found');
        // Open a directory without a package.xml
        $source = new \Pkgtools\Phppear\Source('testsuite/data');
    }

    public function testOpen() {
        // Open test package
        $source = new \Pkgtools\Phppear\Source('testsuite/data/pearpkg1');

        // Pass source to dependents tests
        return $source;
    }

    /**
     * @depends testOpen
     */
    public function testName($source) {
        $this->assertEquals('MyProject_Packages', $source->name);
    }

    /**
     * @depends testOpen
     */
    public function testChannel($source) {
        $this->assertEquals('pear.php.net', $source->channel);
    }

    /**
     * @depends testOpen
     */
    public function testSummary($source) {
        $this->assertEquals('this is a wrapper package for installing dependencies for MyProject.', $source->summary);
    }

    /**
     * @depends testOpen
     */
    public function testDescription($source) {
        $this->assertEquals('<Some random notes>
    This is a paragraph.

    Those are bullets:
    * Number one
    * Number two
 ',
            $source->description);
    }

    /**
     * @depends testOpen
     */
    public function testVersion($source) {
        $this->assertEquals('1.2.1', $source->getVersion('release'));
        $this->assertEquals('1.0', $source->getVersion('api'));
    }

    /**
     * @depends testOpen
     */
    public function testPackagetype($source) {
        $this->assertEquals('php', $source->getPackageType());
    }

    /**
    /**
     * @depends testOpen
     */
    public function testDependencies($source) {
        $wantedDeps = new \Pkgtools\Base\Dependencies();
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require', '', 'php', '5.2.1', NULL);
        $alpha = new \Pkgtools\Base\Dependency('require', 'pear-pear.php.net', 'Alpha', '1.0~a', '2.0~alpha3');
        $alpha->excludeMaxVersion = false;
        $wantedDeps[] = $alpha;
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require', 'pear-pear.php.net', 'Beta', '1.0~b2', '2.0~beta4');
        $rc = new \Pkgtools\Base\Dependency('require', 'pear-pear.php.net', 'RC', '1.0~RC2', '2.0~RC5');
        $rc->excludeMaxVersion = false;
        $wantedDeps[] = $rc;
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require', 'pear-pear.horde.org', 'Horde_Alarm', '2.2.0', '3.0.0~alpha1');
        $phpunit = new \Pkgtools\Base\Dependency('require', 'pear-pear.phpunit.de', 'PHPUnit', '1.1', '1.9');
        $phpunit->excludeMaxVersion = false;
        $wantedDeps[] = $phpunit;
        $wantedDeps[] = new \Pkgtools\Base\Dependency('recommend', 'pear-pecl.php.net', 'mysqli');
        $deps = $source->getDependencies();
        $this->assertEquals($wantedDeps, $deps);
    }
}
