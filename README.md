A simple resource watcher for getting changes of your filesystem.

[![Build Status](https://travis-ci.org/yosymfony/resource-watcher.png?branch=master)](https://travis-ci.org/yosymfony/resource-watcher)
[![Latest Stable Version](https://poser.pugx.org/yosymfony/resource-watcher/v/stable.png)](https://packagist.org/packages/yosymfony/resource-watcher)

## Installation

Use [Composer](http://getcomposer.org/) to install this package:

```bash
composer require yosymfony/resource-watcher
```

## How to use?

This package uses [Symfony Finder](http://symfony.com/doc/current/components/finder.html)
to set the criteria to discover file changes.

```php
use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\Crc32ContentHash;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCachePhpFile;

$finder = new Finder();
$finder->files()
    ->name('*.md')
    ->in(__DIR__);

$hashContent = new Crc32ContentHash();
$resourceCache = new ResourceCachePhpFile('/path-cache-file.php');
$watcher = new ResourceWatcher($resourceCache, $finder, $hashContent);
$watcher->initialize();

// delete a file

$result = $watcher->findChanges();

$result->getDeletedResources() // array of deleted filenames. e.g: "/home/yosymfony/README.md"
```

### Finding changes

Every time the method `findChanges()` of the class `ResourceWatcher` is invoked,
it returns an object type `ResourceWatcherResult` with information about all the
changes producced in the filesystem. The `ResourceWatcherResult` class has the following methods:

* `getNewFiles()`: Returns an array with the paths of the new resources.
* `getDeteledFiles()`: Returns an array with the paths of deleted resources.
* `getUpdatedFiles()`: Returns an array with the paths of the updated resources.
* `hasChanges()`: Are they changes in your resources?.

### Rebuild cache

To rebuild the resource cache uses `rebuild()` method of the class `ResourceWatcher`.

### Relative paths with the resource cache

Using relative paths with the resource cache is possible thanks to the
ResourceWatcher's method `enableRelativePathWithCache`:

```php
$watcher = new ResourceWatcher($resourceCache, $finder, $hashContent);
$watcher->enableRelativePathWithCache()
```

The `ResourceWatcherResult` object will has relative paths.

## Unit tests

You can run the unit tests with the following command:

```bash
$ composer test
```
