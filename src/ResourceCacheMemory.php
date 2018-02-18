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
 * Resource cache implementation using memory as temporal store
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResourceCacheMemory implements ResourceCacheInterface
{
    protected $isInitialized = false;
    protected $data = [];
    
    /**
     * {@inheritdoc}
     */
    public function isInitialized()
    {
        return $this->isInitialized;
    }
    
    /**
     * {@inheritdoc}
     */
    public function read($resourceName)
    {
        return isset($this->data[$resourceName]) ? $this->data[$resourceName] : null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function write($resourceName, $timestamp)
    {
        $this->data[$resourceName] = $timestamp;
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete($resourceName)
    {
        unset($this->data[$resourceName]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function erase()
    {
        $this->data = [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->isInitialized = true;
    }
}
