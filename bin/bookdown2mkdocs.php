#!/usr/bin/env php
<?php
/**
 * @copyright Copyright (c) 2015 Matthew Weier O'Phinney (https://mwop.net)
 * @license   http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */
namespace Phly\Bookdown2Mkdocs;

use Zend\Console\Console;
use ZF\Console\Application;

switch (true) {
    case (file_exists(__DIR__ . '/../vendor/autoload.php')):
        // Installed standalone
        require __DIR__ . '/../vendor/autoload.php';
        break;
    case (file_exists(__DIR__ . '/../../../autoload.php')):
        // Installed as a Composer dependency
        require __DIR__ . '/../../../autoload.php';
        break;
    case (file_exists('vendor/autoload.php')):
        // As a Composer dependency, relative to CWD
        require 'vendor/autoload.php';
        break;
    default:
        throw new RuntimeException('Unable to locate Composer autoloader; please run "composer install".');
}

define('VERSION', '0.1.0-dev');

$routes      = include __DIR__ . '/../config/routes.php';
$application = new Application(
    'Bookdown2Mkdocs',
    VERSION,
    $routes,
    Console::getInstance()
);

$exit = $application->run();
exit($exit);
