<?php
/**
 * @copyright Copyright (c) 2015 Matthew Weier O'Phinney (https://mwop.net)
 * @license   http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */

namespace Phly\Bookdown2Mkdocs;

use Zend\Console\ColorInterface as Color;

class Command
{
    public function __invoke($route, $console)
    {
        $bookdownPath = $route->getMatchedParam('bookdown-path');
        $siteName     = $route->getMatchedParam('site-name');
        $repoUrl      = $route->getMatchedParam('repo-url');
        $copyright    = [
            $route->getMatchedParam('copyright-url'),
            $route->getMatchedParam('copyright-author'),
        ];
        $defaults     = $route->getMatchedParam('mkdocs', []);

        $converter = new Convert();

        $yaml = $converter(
            $bookdownPath,
            $siteName,
            $repoUrl,
            $copyright,
            $defaults
        );

        file_put_contents('./mkdocs.yml', $yaml);
        $console->writeLine('Wrote contents to mkdocs.yml', Color::GREEN);
        return 0;
    }
}
