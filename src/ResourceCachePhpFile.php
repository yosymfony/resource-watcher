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
 * Resource cache implementation using a PHP file with an array.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ResourceCachePhpFile extends ResourceCacheMemory
{
    protected $filename;
    protected $hasPendingChasges = false;

    /**
     * Constructor.
     *
     * @param string $filename The cache ".PHP" file. E.g: "resource-watcher-cache.php"
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
    public function write($filename, $hash)
    {
        if ($hash === $this->read($filename)) {
            return;
        }

        parent::write($filename, $hash);

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
            throw new \RuntimeException(sprintf('Failed to write the file "%s".', $this->filename));
        }
    }

    private function readCacheFile($filename)
    {
        if (false == preg_match('#\.php$#', $filename)) {
            throw new \InvalidArgumentException('The cache filename must ends with the extension ".php".');
        }

        if (file_exists($filename)) {
            $content = include_once($filename);

            if (is_array($content)) {
                return $content;
            }
        }
    }

    private function getContentFile(array $cacheEntries)
    {
        $data = '';

        foreach ($cacheEntries as $filename => $hash) {
            $data .= sprintf('\'%s\'=>%s,', $filename, $hash);
        }

        return "<?php\nreturn [$data];";
    }
}
