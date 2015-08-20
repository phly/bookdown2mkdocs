<?php
/**
 * @copyright Copyright (c) 2015 Matthew Weier O'Phinney (https://mwop.net)
 * @license   http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */

use ZF\Console\Filter\Json as JsonFilter;

/**
 * Console routing configuration
 */
return [
    [
        'name'                 => 'convert',
        'route'                => '[<bookdown-path>] --site-name= --repo-url= --copyright-url= --copyright-author= [--mkdocs=]',
        'description'          => 'Convert a bookdown.json file to mkdocs.yml, typically to allow documentation integration via rtfd.org. The command will write to the mkdocs.yml file in the current directory',
        'short_description'    => 'Convert a bookdown.json file to mkdocs.yml.',
        'options_descriptions' => [
            '[<bookdown-path>]'   => 'Path to bookdown.json; if not present, assumes doc/bookdown.json',
            '--site-name='        => 'Site/project name; typically used as the subdomain in rtfd.org',
            '--repo-url='         => 'Repository URI (linked from generated docs)',
            '--copyright-url='    => 'URL associated with the copyright holder',
            '--copyright-author=' => 'Copyright holder/author',
            '[--mkdocs=]'         => 'Additional default configuration for mkdocs, as a JSON string',
        ],
        'defaults' => [
            'bookdown-path' => 'doc/bookdown.json',
        ],
        'filters' => [
            'mkdocs' => new JsonFilter(),
        ],
        'handler' => 'Phly\Bookdown2Mkdocs\Command',
    ],
];
