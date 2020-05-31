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
use Yosymfony\ResourceWatcher\Crc32MetaDataHash;

class Crc32MetaDataHashTest extends TestCase
{
    public function testHashMustReturnTheMetaDataDigestWithCRC32()
    {
        $filepath = __DIR__ . '/test.txt';

        touch($filepath, strtotime('2020-05-25 17:42'));

        $crc32ContentHash = new Crc32MetaDataHash();
        $currentValue = $crc32ContentHash->hash($filepath);

        $this->assertEquals('0b93d57a', $currentValue);

        unlink($filepath);
    }
}
