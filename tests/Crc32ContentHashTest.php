<?php

/*
 * This file is part of the Yo! Symfony Resource Watcher.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ResourceWatcher\Tests;

use PHPUnit\Framework\TestCase;
use Yosymfony\ResourceWatcher\Crc32ContentHash;

class Crc32ContentHashTest extends TestCase
{
    public function testHashMustReturnTheContentDisgestWithCRC32()
    {
        $crc32ContentHash = new Crc32ContentHash();
        $currentValue = $crc32ContentHash->hash('acme');

        $this->assertEquals('8f7ecb57', $currentValue);
    }
}
