<?php

/*
 * This file is part of the Yosymfony\ResourceWatcher.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Yosymfony\ResourceWatcher;

/**
 * Resource cache implementation using PHP file with an array
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResourceCacheFile extends ResourceCacheMemory
{
    protected $filename;
    protected $hasPendingChasges = false;
    
    /**
     * Constructor
     *
     * @param string $filename PHP filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        
        $cacheContent = $this->readCacheFile($this->filename);
        
        if ($cacheContent) {
            $this->data = $cacheContent;
            $this->isInitialized = true;
        } else {
            $this->hasPendingChasges = true;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function write($resourceName, $timestamp)
    {
        if ($timestamp == $this->read($resourceName)) {
            return;
        }
        
        parent::write($resourceName, $timestamp);
        
        $this->hasPendingChasges = true;
        $this->isInitialized = true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function save()
    {
        if (false == $this->hasPendingChasges) {
            return;
        }
        
        $content = $this->getContentFile($this->data);
        
        $this->hasPendingChasges = false;
        $this->isInitialized = true;
        
        if (false === @file_put_contents($this->filename, $content)) {
            throw new \RuntimeException(sprintf('Failed to write file "%s".', $this->filename));
        }
    }
    
    private function readCacheFile($filename)
    {
        if (false == preg_match('#\.php$#', $filename)) {
            throw new \InvalidArgumentException('The cache filename must ends with php extension');
        }
        
        if (file_exists($filename)) {
            $content = include_once($filename);
            
            if (is_array($content)) {
                return $content;
            }
        }
    }
    
    private function getContentFile(array $resources)
    {
        $data = '';
        
        foreach ($resources as $resourceName => $timestamp) {
            $data .= sprintf('\'%s\'=>%s,', $resourceName, $timestamp);
        }
        
        return "<?php\nreturn [$data];";
    }
}
