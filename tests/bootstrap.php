<?php

declare(strict_types = 1);

namespace Achse\ShapeShiftIo\Test;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/MyAssert.php';

use Tester\Environment;

if (!class_exists('Tester\Assert')) {
    echo "Install Nette Tester using `composer update --dev`\n";
    exit(1);
}

Environment::setup();
