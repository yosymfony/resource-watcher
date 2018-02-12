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
for setting the criteria for finding resources.

```php
use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\ResourceWatcher;
use Yosymfony\ResourceWatcher\ResourceCacheFile;

$finder = new Finder();
$finder->files()
    ->name('*.php')
    ->depth(0)
    ->size('>= 1K')
    ->in(__DIR__);

$rc = new ResourceCacheFile('/path-to-cache-file.php');

$rw = new ResourceWatcher($rc);
$rw->setFinder($finder);

$rw->findChanges();

$rw->getNewResources()
$rw->getDeletedResources()
$rw->getUpdatedResources()

// delete a file

$rw->findChanges();

$rw->getDeletedResources() // array with pathname of deleted files
```

## finding changes

Every time that you call `findChanges()` from `ResourceWatcher` you are getting the changes
producced by your filesystem. The resources changed can be recovered with following methods:

* `getNewResources()`: Return an array with the paths of the new resources.
* `getDeteledResources()`: Return an array with the paths of deleted resources.
* `getUpdatedResources()`: Return an array with the paths of the updated resources.
* `hasChanges()`: Has changes in your resources?.
* `isSearching()`: Is searching changes in your resources?.

## Rebuild cache

To rebuild the resource cache uses `rebuild()` method from `ResourceWatcher`.

## Unit tests

You can run the unit tests with the following command:

```bash
$ cd your-path/vendor/yosymfony/resource-watcher
$ composer.phar install --dev
$ phpunit
```
