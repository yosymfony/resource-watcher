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

use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\Crc32ContentHash;
use Yosymfony\ResourceWatcher\ResourceCachePhpFile;
use Yosymfony\ResourceWatcher\ResourceWatcher;

class ResourceWatcherWithPhpFileTest extends ResourceWatcherTest
{
    public function testFindChangesMustCatchFilesWithAccents()
    {
        $filepath = $this->tmpDir . "/file1 - O'Connor.txt";
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);

        $resourceWatcher = $this->makeResourceWatcher($finder);
        $resourceWatcher->findChanges();

        $this->fs->dumpFile($filepath, 'test');
        $result = $resourceWatcher->findChanges();

        $this->assertCount(1, $result->getNewFiles());


        $this->fs->remove($filepath);
        $result = $resourceWatcher->findChanges();
        $this->assertCount(1, $result->getDeletedFiles());
    }

    protected function makeResourceWatcher(Finder $finder)
    {
        $cacheFile = sys_get_temp_dir() . '/resource-watcher-cache.php';

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        $cacheMemory = new ResourceCachePhpFile($cacheFile);
        $contentHashCrc32 = new Crc32ContentHash();

        return new ResourceWatcher($cacheMemory, $finder, $contentHashCrc32);
    }
}
