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
* This class tests basic functionnality of \Pkgtools\Phpcomposer\Source
*
* @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
* @author Mathieu Parent <sathieu@debian.org>
* @license Expat http://www.jclark.com/xml/copying.txt
*/
class PhpcomposerSourceTest extends PHPUnit\Framework\TestCase {
    public function testFailedOpen() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('composer.json not found');
        // Open a directory without a composer.json
        $source = new \Pkgtools\Phpcomposer\Source('testsuite/data');
    }

    public function testBrokenOpen() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Error parsing composer.json: Syntax error, malformed JSON (Syntax error)');
        // Open a directory with a broken composer.json
        $source = new \Pkgtools\Phpcomposer\Source('testsuite/data/broken');
    }

    public function testOpen() {
        // Open test package
        $source = new \Pkgtools\Phpcomposer\Source('testsuite/data/composerpkg1');

        // Pass source to dependents tests
        return $source;
    }

    /**
     * @depends testOpen
     */
    public function testName($source) {
        $this->assertEquals('vendor/project', $source->name);
    }

    /**
     * @depends testOpen
     */
    public function testDescription($source) {
        $this->assertEquals('Some project, from some vendor, from somewhere. Also with a very long line to check for that too ;-).', $source->description);
    }

    /**
     * @depends testOpen
     */
    public function testDependencies($source) {
        $wantedDeps = new \Pkgtools\Base\Dependencies();
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require', '', 'php');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require', '', 'php', '5.2', NULL);
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require', 'proj1', 'pack1', '2.0', '3~~');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'spl');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('require-dev', 'overrideme', 'overrideme-sub', '3.7', '3.8~~');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('recommend', 'ocramius', 'proxy-manager', '0.3.1', '0.4~~');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('suggest', 'pear-pecl.php.net', 'apc');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('suggest', 'pear-pecl.php.net', 'curl');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('provide', 'example', 'project', '1.7', '1.8~~');
        $wantedDeps[] = new \Pkgtools\Base\Dependency('provide', 'pear-pecl.php.net', 'curl');
        $deps = $source->getDependencies();
        $this->assertEquals($wantedDeps, $deps);
    }
}
