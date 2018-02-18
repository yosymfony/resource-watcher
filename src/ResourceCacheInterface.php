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
 * Interface for ResourceCache objects
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ResourceCacheInterface
{
    /**
     * If cache isInitialized? if not then warm-up cache
     *
     * @return bool
     */
    public function isInitialized();
    
    /**
     * Get the timestamp of a resource
     *
     * @param string $resourceName
     *
     * @return int | null if not exists
     */
    public function read($resourceName);
    
    /**
     * Write or update a resource
     *
     * @param string $resourceName
     * @param int $timestamp
     */
    public function write($resourceName, $timestamp);
    
    /**
     * Delete a resource
     *
     * @param string $resourceName
     */
    public function delete($resourceName);
    
    /**
     * Erase all elements in cache
     */
    public function erase();
    
    /**
     * Return an array of items with resource-name as key and timestamp as value
     *
     * @return array
     */
    public function getResources();
    
    /**
     * Persist the cache
     */
    public function save();
}
