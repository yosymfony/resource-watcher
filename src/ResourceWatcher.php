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

use Symfony\Component\Finder\Finder;

/**
 * A simple resource-watcher to discover changes in the filesystem.
 * This component uses Symfony Finder to set the file search criteria.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResourceWatcher
{
    private $cache;
    private $finder;
    private $contentHash;
    private $fileHashesFromFinder = [];
    private $newFiles = [];
    private $deletedFiles = [];
    private $updatedFiles = [];

    /**
     * Constructor.
     *
     * @param ResourceCacheInterface $resourceCache The cache.
     * @param Finder $finder The Symfony Finder.
     * @param ContentHashInterface $contentHash The file hash strategy.
     */
    public function __construct(ResourceCacheInterface $resourceCache, Finder $finder, ContentHashInterface $contentHash)
    {
        $this->cache = $resourceCache;
        $this->finder = $finder;
        $this->contentHash = $contentHash;
    }

    /**
     * Finds all changes in the filesystem according to the finder criteria.
     */
    public function findChanges()
    {
        $this->reset();

        if (false == $this->cache->isInitialized()) {
            $this->warmUpCache();
        } else {
            $this->findChangesAgainstCache();
        }

        $this->cache->save();

        return new ResourceWatcherResult($this->newFiles, $this->deletedFiles, $this->updatedFiles);
    }

    /**
     * Rebuild the resource cache
     */
    public function rebuild()
    {
        $this->cache->erase();
        $this->reset();
        $this->warmUpCache();
        $this->cache->save();
    }

    private function reset()
    {
        $this->newFiles = [];
        $this->deletedFiles = [];
        $this->updatedFiles = [];
    }

    private function warmUpCache()
    {
        foreach ($this->finder as $file) {
            $filePath = $file->getPathname();
            $this->cache->write($filePath, $this->calculateHashOfFile($filePath));
        }
    }

    private function findChangesAgainstCache()
    {
        $this->calculateHashOfFilesFromFinder();

        $finderFileHashes = $this->fileHashesFromFinder;
        $cacheFileHashes = $this->cache->getResources();

        if (count($finderFileHashes) > count($cacheFileHashes)) {
            foreach ($finderFileHashes as $file => $hash) {
                $this->processFileFromFilesystem($file, $hash);
            }
        } else {
            foreach ($cacheFileHashes as $file => $hash) {
                $this->processFileFromCache($file, $hash);
            }
        }
    }

    private function processFileFromFilesystem($file, $hash)
    {
        $hashFromCache = $this->cache->read($file);

        if ($hashFromCache) {
            if ($hash != $hashFromCache) {
                $this->cache->write($file, $hash);
                $this->updatedFiles[] = $file;
            }
        } else {
            $this->cache->write($file, $hash);
            $this->newFiles[] = $file;
        }
    }

    private function processFileFromCache($file, $hash)
    {
        $hashFromCache = isset($this->fileHashesFromFinder[$file]) ? $this->fileHashesFromFinder[$file] : null;

        if ($hashFromCache) {
            if ($hashFromCache != $hash) {
                $this->cache->write($file, $hashFromCache);
                $this->updatedFiles[] = $file;
            }
        } else {
            $this->cache->delete($file);
            $this->deletedFiles[] = $file;
        }
    }

    private function calculateHashOfFilesFromFinder()
    {
        $pathsAndHashes = [];

        foreach ($this->finder as $file) {
            $filePath = $file->getPathname();
            $pathsAndHashes[$filePath] = $this->calculateHashOfFile($filePath);
        }

        $this->fileHashesFromFinder = $pathsAndHashes;
    }

    private function calculateHashOfFile($filename)
    {
        $fileContent = file_get_contents($filename);

        return $this->contentHash->hash($fileContent);
    }
}
