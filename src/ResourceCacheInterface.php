<?php

/*
 * This file is part of the Yo! Symfony Resource Watcher.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ResourceWatcher;

/**
 * Interface of a resource cache.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ResourceCacheInterface
{
    /**
     * If the cache Initialized? if not then warm-up cache.
     *
     * @return bool
     */
    public function isInitialized();

    /**
     * Returns the hash of a file in cache.
     *
     * @param string $filename
     *
     * @return string The hash for the filename. Empty string if not exists.
     */
    public function read($filename);

    /**
     * Updates the hash of a file in cache.
     *
     * @param string $filename
     * @param string $hash The calculated hash for the filename.
     */
    public function write($filename, $hash);

    /**
     * Deletes a file in cache.
     *
     * @param string $filename
     */
    public function delete($filename);

    /**
     * Erases all the elements in cache.
     */
    public function erase();

    /**
     * Returns an array of key-values with the file as key and the hash as value.
     *
     * @return array
     */
    public function getResources();

    /**
     * Persists the cache
     */
    public function save();
}
