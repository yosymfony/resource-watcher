2.0.0
-----
* [New] The class `ResourceWatcher` accepts two new arguments: the finder and class that makes content hash.
* [New] The class `ResourceWatcher` uses content hash instead of dates to detect changes.
* [New] The method `findChanges` from the class `ResourceWatcher` returns an object type `ResourceWatcherResult` with all the information about files changes.
* [New] Added a new method `initialize` to the class  `ResourceWatcher`. This method warm up the cache just in case.
* [Delete] Deleted the method `isSearching` from the class `ResourceWatcher`.
* [Delete] Deleted the method `setFinder` from the class `ResourceWatcher`. Now, the finder is passed as constructor argument.
