<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CompareUtility\Compare;

// Compare::dump(dirname(__FILE__));
// Compare::dump(realpath(__DIR__));

$root = dirname(dirname(__FILE__));

Compare::setRootPath($root);
Compare::setScanList([ $root ]);

Compare::setIgnoreEveryList(['.git']);
Compare::setIgnoreList([ $root.'/vendor']);

Compare::dump(Compare::getScanList());
$data = Compare::scan();
Compare::dump($data);