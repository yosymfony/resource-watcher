A simple resource watcher for getting changes in your filesystem.

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

## Unit tests

You can run the unit tests with the following command:

    $ cd your-path/vendor/yosymfony/resource-watcher
    $ composer.phar install --dev
    $ phpunit
