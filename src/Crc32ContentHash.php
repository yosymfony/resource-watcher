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
 * CRC32 content hash implementation.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Crc32ContentHash implements ContentHashInterface
{
    /**
     * {@inheritdoc}
     */
    public function hash($content)
    {
        return hash('crc32', $content);
    }
}
