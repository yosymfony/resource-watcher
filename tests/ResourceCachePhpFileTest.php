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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Yosymfony\ResourceWatcher\ResourceCachePhpFile;

class ResourceCachePhpFileTest extends TestCase
{
    private $cacheFile;
    private $fs;
    private $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir() . '/resource-watchers-tests';
        $this->cacheFile = $this->tmpDir . '/cache-file-test.php';
        $this->fs = new Filesystem();
        $this->fs->mkdir($this->tmpDir);
    }

    public function tearDown()
    {
        $this->fs->remove($this->tmpDir);
    }

    public function testIsInitializedMustReturnFalseWhenTheCacheFileIsNotExists()
    {
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);

        $this->assertFalse($resourceCache->isInitialized());
    }

    public function testIsInitializedMustReturnTrueWhenTheCacheIsSavedInTheCacheFile()
    {
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);
        $resourceCache->save();

        $this->assertTrue($resourceCache->isInitialized());
    }

    public function testIsInitializedMustReturnTrueWhenThereIsAValidCacheFile()
    {
        $this->fs->dumpFile($this->cacheFile, "<?php\nreturn [];");
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);

        $this->assertTrue($resourceCache->isInitialized());
    }

    public function testSaveMustDumpTheContentCacheInAFile()
    {
        $resourceCache = new ResourceCachePhpFile($this->cacheFile);
        $filename = 'file1.md';
        $hash = '23998C';
        $resourceCache->write($filename, $hash);
        $resourceCache->save();

        $fileContent = file_get_contents($this->cacheFile);

        $this->assertEquals($fileContent, "<?php\nreturn ['$filename'=>'$hash',];");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Cache file invalid format.
     */
    public function testConstructWithAInvalidCacheFileMustThrownAnException()
    {
        $this->fs->dumpFile($this->cacheFile, '');

        $rc = new ResourceCachePhpFile($this->cacheFile);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The cache filename must ends with the extension ".php".
     */
    public function testConstructWithANoPhpFileExtensionMustThrownAnException()
    {
        $rc = new ResourceCachePhpFile($this->tmpDir . '/cache-file-test.txt');
    }
}
