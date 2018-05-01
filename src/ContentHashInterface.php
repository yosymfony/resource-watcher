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
 * Interface for hashing content.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface ContentHashInterface
{
    /**
     * Calculates the hash of the content.
     *
     * @param string $content  Message to be hashed.
     *
     * @return string Returns a string containing the calculated message digest.
     */
    public function hash($content);
}
