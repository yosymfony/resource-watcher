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
use Yosymfony\ResourceWatcher\ResourceCacheFile;

class ResourceCacheFileTest extends \PHPUnit_Framework_TestCase
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
    
    public function testCreate()
    {
        $rc = new ResourceCacheFile($this->tmpDir . '/cache-file-test.php');
        
        $this->assertFalse($rc->isInitialized());
        
        $rc = new ResourceCacheFile($this->tmpDir . '/cache-file-test.php');
        $rc->save();
        
        $this->assertTrue($rc->isInitialized());
    }
    
    public function testSaveResources()
    {
        $rc = new ResourceCacheFile($this->tmpDir . '/cache-file-test.php');
        $rc->write('/resource-1/file1.txt', 3455345);
        $rc->write('/resource-1/file2.txt', 945635);
        $rc->save();
        
        $rc = new ResourceCacheFile($this->tmpDir . '/cache-file-test.php');
        
        $this->assertCount(2, $rc->getResources());
        $this->assertEquals(945635, $rc->read('/resource-1/file2.txt'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoPhpFile()
    {
        $rc = new ResourceCacheFile($this->tmpDir . '/cache-file-test.txt');
    }
}
