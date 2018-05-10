<?php

/*
 * This file is part of the Yo! Symfony Resource Watcher.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ResourceWatcher\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\ResourceWatcher\ResourceCachePhpFile;

class ResourceCachePhpFileTest extends \PHPUnit_Framework_TestCase
{
    private $cacheFile;
    private $tmpDir;
    private $fs;
    private $resourceCache;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir() . '/resource-watchers-tests';
        $this->fs = new Filesystem();
        $this->fs->mkdir($this->tmpDir);
        $this->cacheFile = $this->tmpDir . '/cache-file-test.php';
        $this->resourceCache = new ResourceCachePhpFile($this->cacheFile);
    }

    public function tearDown()
    {
        $this->fs->remove($this->tmpDir);
    }

    public function testIsInitializedMustReturnFalseWhenTheCacheFileIsNotExists()
    {
        $this->assertFalse($this->resourceCache->isInitialized());
    }

    public function testIsInitializedMustReturnTrueWhenTheCacheIsSavedInTheCacheFile()
    {
        $this->resourceCache->save();

        $this->assertTrue($this->resourceCache->isInitialized());
    }

    public function testSaveMustDumpTheContentCacheInAFile()
    {
        $filename = 'file1.md';
        $hash = '23998';
        $this->resourceCache->write($filename, $hash);
        $this->resourceCache->save();
        $rc = new ResourceCachePhpFile($this->cacheFile);

        $this->assertCount(1, $rc->getAll());
        $this->assertEquals($hash, $rc->read($filename));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The cache filename must ends with the extension ".php".
     */
    public function testConstructResourceCachePhpFileWithANoPhpFileMustThrownAnException()
    {
        $rc = new ResourceCachePhpFile($this->tmpDir . '/cache-file-test.txt');
    }
}
