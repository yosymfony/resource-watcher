A simple resource watcher for getting changes of your filesystem.

[![Build Status](https://travis-ci.org/yosymfony/Resource-watcher.png?branch=master)](https://travis-ci.org/yosymfony/Resource-watcher)
[![Latest Stable Version](https://poser.pugx.org/yosymfony/resource-watcher/v/stable.png)](https://packagist.org/packages/yosymfony/resource-watcher)

## Installation

Use [Composer](http://getcomposer.org/) to install `resource-watcher` package:

Add the following to your `composer.json` and run `composer update`.

```json
"require": {
    "yosymfony/resource-watcher": "1.0.x-dev"
}
```

More information about the package on [Packagist](https://packagist.org/packages/yosymfony/resource-watcher).

## How to use?

This component uses [Symfony Finder](http://symfony.com/doc/current/components/finder.html) 
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

$rw = new ResourceWatcher($c);
$rw->setFinder($finder);

$rw->findChanges();

$rw->getNewResources()
$rw->getDeteledResources()
$rw->getUpdatedResources()

// delete a file

$rw->findChanges();

$rw->getDeteledResources() // array with pathname of deleted files
```

## finding changes

Every time that you call `findChanges()` from `ResourceWatcher` you are getting the changes
producced by your filesystem. The resources changed can be recovered with these methods:

* `getNewResources()`: Return an array with the paths of the new resources.
* `getDeteledResources()`: Return an array with the paths of deleted resources.
* `getUpdatedResources()`: Return an array with the paths of the updated resources.

## Rebuild cache

To rebuild the resource cache uses `rebuild()` method from `ResourceWatcher`.

## Unit tests

You can run the unit tests with the following command:

```bash
$ cd your-path/vendor/yosymfony/resource-watcher
$ composer.phar install --dev
$ phpunit
```