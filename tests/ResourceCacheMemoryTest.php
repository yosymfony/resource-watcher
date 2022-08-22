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
use Yosymfony\ResourceWatcher\ResourceCacheMemory;

class ResourceCacheMemoryTest extends TestCase
{
    private $cache;

    public function setUp(): void
    {
        $this->cache = new ResourceCacheMemory();
    }

    public function testIsInitializedMustReturnFalseInTheInitialState(): void
    {
        $this->assertFalse($this->cache->isInitialized());
    }

    public function testIsInitializedMustReturnTrueAfterSave(): void
    {
        $this->cache->save();

        $this->assertTrue($this->cache->isInitialized());
    }

    public function testGetAllMustReturnAllFilesInCache(): void
    {
        $this->cache->write('/my-path/file1.txt', '2442345');

        $this->assertCount(1, $this->cache->getAll());
    }

    public function testReadMustReturnTheHashOfTheFile(): void
    {
        $hash = 'a54ffa';
        $this->cache->write('/my-path/file1.txt', $hash);

        $this->assertEquals($hash, $this->cache->read('/my-path/file1.txt'));
    }

    public function testWriteMustAddANewFile(): void
    {
        $hash = 'a54ffa';
        $this->cache->write('/my-path/file1.txt', $hash);

        $this->assertEquals($hash, $this->cache->read('/my-path/file1.txt'));
    }

    public function testWriteMustUpdateAFilePreviouslyAdded(): void
    {
        $hash = 'b54ffb';
        $file = '/my-path/file1.txt';
        $this->cache->write($file, 'a54ffa');
        $this->cache->write($file, $hash);

        $this->assertEquals($hash, $this->cache->read('/my-path/file1.txt'));
    }

    public function testEraseMustDeleteAllFiles(): void
    {
        $this->cache->write('/my-path/file1.txt', 'a54ffa');
        $this->cache->erase();

        $this->assertCount(0, $this->cache->getAll());
    }

    public function testDeleteMustDeleteTheFileIndicated(): void
    {
        $file = '/my-path/file1.txt';
        $this->cache->write($file, 'a54ffa');
        $this->cache->delete($file);

        $this->assertCount(0, $this->cache->getAll());
    }
}
