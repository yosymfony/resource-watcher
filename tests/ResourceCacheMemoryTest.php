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

use Yosymfony\ResourceWatcher\ResourceCacheMemory;

class ResourceCacheMemoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $rc = new ResourceCacheMemory();
        
        $this->assertFalse($rc->isInitialized());
        
        $rc->save();
        
        $this->assertTrue($rc->isInitialized());
    }
    
    public function testArrayResources()
    {
        $rc = new ResourceCacheMemory();
        
        $rc->write('/my-path/file1.txt', 2442345);
        
        $this->assertCount(1, $rc->getResources());
    }
    
    public function testValues()
    {
        $rc = new ResourceCacheMemory();
        
        $rc->write('/my-path/file1.txt', 2442345);
        $rc->write('/my-path/file2.txt', 2442346);
        
        $this->assertEquals(2442345, $rc->read('/my-path/file1.txt'));
        
        $rc->delete('/my-path/file1.txt');
        
        $this->assertNull($rc->read('/my-path/file1.txt'));
        $this->assertCount(1, $rc->getResources());
        
        $rc->erase();
        
        $this->assertCount(0, $rc->getResources());
    }
}
