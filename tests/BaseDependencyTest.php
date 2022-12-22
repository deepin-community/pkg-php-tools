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
 * This class tests basic functionnality of \Pkgtools\Base\Dependency.php
 *
 * @copyright Copyright (c) 2014 Mathieu Parent <sathieu@debian.org>
 * @author Mathieu Parent <sathieu@debian.org>
 * @license Expat http://www.jclark.com/xml/copying.txt
 */
class BaseDependencyTest extends PHPUnit\Framework\TestCase {
    public function testGetUnknownProperty() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unknown property: \'unknown\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->unknown;
    }

    public function testSetUnknownProperty() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unknown property: \'unknown\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->unknown = 'unknown';
    }

    public function testInvalidLevel() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unknown dependency level: \'enhance\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->level = 'enhance';
    }

    public function testInvalidProject() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Malformed dependency project: \'invalid/project\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->project = 'invalid/project';
    }

    public function testInvalidPearExtension() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Invalid dependency project: pear-extension');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->project = 'pear-extension';
    }

    public function testInvalidPackage() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Malformed dependency package: \'inv@lid\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->package = 'inv@lid';
    }

    public function testInvalidVersion() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Malformed dependency minVersion: \'1~béta\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->minVersion = '1~béta';
    }

    public function testInvalidExcludeVersion() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Malformed dependency excludeMinVersion: \'notbool\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->excludeMinVersion = 'notbool';
    }

    public function testInvalidOriginal() {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Malformed dependency original: \'notdep\'');
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->original = 'notdep';
    }

    public function testPackageBeginsWithProject() {
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'project1', 'project1-package1');
        $this->assertEquals('php-project1-package1', $dep->debName());
    }

    public function testMergeProjectPackage() {
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'project1-something1', 'something1-package1');
        $this->assertEquals('php-project1-something1-package1', $dep->debName());
    }

    public function testMaxAsString() {
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->maxVersion = '1';
        $this->assertEquals('require:pear-pecl.php.net/json (< 1)', (string) $dep);
    }

    public function testMaxAsDebName() {
        $this->expectOutputString('');
        $dep = new \Pkgtools\Base\Dependency('require', 'pear-pecl.php.net', 'json');
        $dep->maxVersion = '1';
        $this->assertEquals('php-json (<< 1)', $dep->debDependency());
    }
}
