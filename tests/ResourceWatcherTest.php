<?php

/*
 * This file is part of the Yosymfony\ResourceWatcher.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\ResourceWatcher\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;

class ResourceWatcherTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;
    protected $fs;
    
    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir() . '/resource-watchers-tests';
        $this->fs = new Filesystem();
        
        $this->fs->mkdir($this->tmpDir);
    }
    
    public function tearDown()
    {
        $this->fs->remove($this->tmpDir);
    }
    
    public function testFirstTime()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
    
        $this->assertCount(0, $rw->getNewResources());
        $this->assertCount(0, $rw->getUpdatedResources());
        $this->assertCount(0, $rw->getDeletedResources());
        $this->assertFalse($rw->hasChanges());
    }
    
    public function testFirstTimeWithExistingResources()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
    
        $this->assertCount(0, $rw->getNewResources());
        $this->assertCount(0, $rw->getUpdatedResources());
        $this->assertCount(0, $rw->getDeletedResources());
        $this->assertFalse($rw->hasChanges());
    }
    
    public function testNewResources()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
        
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        $this->fs->dumpFile($this->tmpDir . '/file2.txt', 'test');
        $this->fs->dumpFile($this->tmpDir . '/file3.txt', 'test');
        
        $rw->findChanges();
        
        $this->assertCount(3, $rw->getNewResources());
        $this->assertCount(0, $rw->getUpdatedResources());
        $this->assertCount(0, $rw->getDeletedResources());
    }
    
    public function testDeletedResources()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
        
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        
        $rw->findChanges();
        
        $this->fs->remove($this->tmpDir . '/file1.txt');
        
        $rw->findChanges();
        
        $this->assertCount(0, $rw->getNewResources());
        $this->assertCount(0, $rw->getUpdatedResources());
        $this->assertCount(1, $rw->getDeletedResources());
    }
    
    public function testUpdatedResources()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
        
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        
        $rw->findChanges();
        
        $this->assertCount(1, $rw->getNewResources());
        
        $this->fs->touch($this->tmpDir . '/file1.txt', time() + 100);
        
        $rw->findChanges();
        
        $this->assertCount(0, $rw->getNewResources());
        $this->assertCount(1, $rw->getUpdatedResources());
        $this->assertCount(0, $rw->getDeletedResources());
    }
    
    public function testAllChanges()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
        
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        $this->fs->dumpFile($this->tmpDir . '/file2.txt', 'test');
        $this->fs->dumpFile($this->tmpDir . '/file3.txt', 'test');
        
        $rw->findChanges();
        
        $newResources = $rw->getNewResources();
        
        $this->assertCount(3, $newResources);
        $this->assertContains($this->tmpDir . '/file1.txt', $newResources);
        $this->assertContains($this->tmpDir . '/file2.txt', $newResources);
        $this->assertContains($this->tmpDir . '/file3.txt', $newResources);
        
        // ---
        
        $this->fs->touch($this->tmpDir . '/file1.txt', time() + 100);
        $this->fs->remove($this->tmpDir . '/file2.txt');
        
        $rw->findChanges();
        
        $updatedResources = $rw->getUpdatedResources();
        $deletedResources = $rw->getDeletedResources();
        
        $this->assertCount(0, $rw->getNewResources());
        $this->assertCount(1, $updatedResources);
        $this->assertCount(1, $deletedResources);
        $this->assertEquals($this->tmpDir . '/file1.txt', $updatedResources[0]);
        $this->assertEquals($this->tmpDir . '/file2.txt', $deletedResources[0]);
    }
    
    public function testNewFolder()
    {
        $finder = new Finder();
        $finder->in($this->tmpDir);
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
        
        $this->fs->mkdir($this->tmpDir . '/dir-test');
        
        $rw->findChanges();
        
        $newResources = $rw->getNewResources();
        
        $this->assertCount(1, $newResources);
        $this->assertCount(0, $rw->getUpdatedResources());
        $this->assertCount(0, $rw->getDeletedResources());
        $this->assertEquals($this->tmpDir . '/dir-test', $newResources[0]);
    }
    
    public function testRebuild()
    {
        $finder = new Finder();
        $finder->files()
            ->name('*.txt')
            ->in($this->tmpDir);
        
        $rc = new ResourceCacheMemory();
        $rw = new ResourceWatcher($rc);
        $rw->setFinder($finder);
        
        $this->fs->dumpFile($this->tmpDir . '/file1.txt', 'test');
        $this->fs->dumpFile($this->tmpDir . '/file2.txt', 'test');
        $this->fs->dumpFile($this->tmpDir . '/file3.txt', 'test');
        
        $rw->rebuild();
        $rw->findChanges();
        
        $this->assertCount(0, $rw->getNewResources());
        $this->assertCount(0, $rw->getUpdatedResources());
        $this->assertCount(0, $rw->getDeletedResources());
    }
}
