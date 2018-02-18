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

use Symfony\Component\Finder\Finder;

/**
 * A simple resource-watcher for getting changes in resources of filesystem.
 * This component uses Symfony Finder for setting the criteria for finding resources.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResourceWatcher
{
    private $rc;
    private $finder;
    private $isSearchingChanges = false;
    private $resourcesFinder = [];
    private $newResources = [];
    private $deletedResources = [];
    private $updatedResources = [];
    
    /**
     * Constructor
     *
     * @param ResourceCacheInterface $resourceCache
     */
    public function __construct(ResourceCacheInterface $resourceCache)
    {
        $this->rc = $resourceCache;
    }
    
    /**
     * Set the Finder
     *
     * @param Finder $finder
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
        $this->findChanges();
    }
    
    /**
     * Find changes in your resources
     */
    public function findChanges()
    {
        if ($this->isSearchingChanges) {
            return;
        }
        
        $this->isSearchingChanges = true;
        
        $this->reset();
        
        if (false == $this->rc->isInitialized()) {
            $this->warmUpResourceCache();
        } else {
            $this->findChangesAgainstCache();
        }
        
        $this->rc->save();
        
        $this->isSearchingChanges = false;
    }
    
    /**
     * Is searching changes?
     *
     * @return bool
     */
    public function isSearching()
    {
        return $this->isSearchingChanges;
    }
    
    /**
     * Has changes in your resources?
     *
     * @return bool
     */
    public function hasChanges()
    {
        return count($this->newResources) > 0 || count($this->deletedResources) > 0 || count($this->updatedResources) > 0;
    }
    
    /**
     * Get an array with path to the new resources ('.', '..' not resolved).
     *
     * @return array
     */
    public function getNewResources()
    {
        return $this->newResources;
    }
    
    /**
     * Get an array with path of deleted resources ('.', '..' not resolved).
     *
     * @return array
     */
    public function getDeletedResources()
    {
        return $this->deletedResources;
    }
    
    /**
     * Get an array with path to the updated resources ('.', '..' not resolved).
     *
     * @return array
     */
    public function getUpdatedResources()
    {
        return $this->updatedResources;
    }
    
    /**
     * Rebuild the resource cache
     */
    public function rebuild()
    {
        $this->rc->erase();
        $this->reset();
        $this->warmUpResourceCache();
        $this->rc->save();
    }
    
    private function reset()
    {
        $this->newResources = [];
        $this->deletedResources = [];
        $this->updatedResources = [];
    }
    
    private function warmUpResourceCache()
    {
        foreach ($this->finder as $resource) {
            $this->rc->write($resource->getPathname(), $resource->getMTime());
        }
    }
    
    private function findChangesAgainstCache()
    {
        $this->processResourcesFromFinder();

        $resourcesFs = $this->resourcesFinder;
        $resourcesCache = $this->rc->getResources();
        
        if (count($resourcesFs) > count($resourcesCache)) {
            foreach ($resourcesFs as $resourceFs => $timestampFs) {
                $this->processResourceFs($resourceFs, $timestampFs);
            }
        } else {
            foreach ($resourcesCache as $resourceCache => $timestampCache) {
                $this->processResourceCache($resourceCache, $timestampCache);
            }
        }
    }
    
    private function processResourceFs($resourceFs, $timestampFs)
    {
        $timestampCache = $this->rc->read($resourceFs);
        
        if ($timestampCache) {
            if ($timestampFs > $timestampCache) {
                $this->rc->write($resourceFs, $timestampFs);
                $this->updatedResources[] = $resourceFs;
            }
        } else {
            $this->rc->write($resourceFs, $timestampFs);
            $this->newResources[] = $resourceFs;
        }
    }
    
    private function processResourceCache($resourceCache, $timestampCache)
    {
        $timestampFs = isset($this->resourcesFinder[$resourceCache]) ? $this->resourcesFinder[$resourceCache] : null;
        
        if ($timestampFs) {
            if ($timestampFs > $timestampCache) {
                $this->rc->write($resourceCache, $timestampFs);
                $this->updatedResources[] = $resourceCache;
            }
        } else {
            $this->rc->delete($resourceCache);
            $this->deletedResources[] = $resourceCache;
        }
    }
    
    private function processResourcesFromFinder()
    {
        $paths = [];
        
        foreach ($this->finder as $resource) {
            clearstatcache();
            $paths[$resource->getPathname()] = $resource->getMTime();
        }
        
        $this->resourcesFinder = $paths;
    }
}
