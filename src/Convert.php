<?php
/**
 * @copyright Copyright (c) 2015 Matthew Weier O'Phinney (https://mwop.net)
 * @license   http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 */

namespace Phly\Bookdown2Mkdocs;

use Symfony\Component\Yaml\Yaml;

class Convert
{
    private $copyrightTemplate = 'Copyright (c) %s <a href="%s">%s</a>';

    private $mkdocsDefaults = [
        'markdown_extensions' => [
            'py-gfm',
        ],
        'docs_dir' => 'doc/book',
        'site_dir' => 'doc/html',
        'pages' => [
            'index.md',
        ],
    ];

    /**
     * @param string $bookdownPath
     * @param string $siteName
     * @param string $repoUrl
     * @param array $copyright Array containing two elements, in order: URL for
     *     copyright holder, and copyright holder name.
     * @param array $mkdocsDefaults Any additional default information to use
     *     for the mkdocs configuration.
     * @return string YAML for mkdocs.yml configuration
     */
    public function __invoke(
        $bookdownPath,
        $siteName,
        $repoUrl,
        array $copyright,
        array $mkdocsDefaults = []
    ) {
        $bookdown = json_decode(file_get_contents($bookdownPath));
        $config   = array_merge($this->mkdocsDefaults, $mkdocsDefaults);
        array_unshift($copyright, date('Y'));

        $config['site_name']        = $siteName;
        $config['site_description'] = $bookdown->title;
        $config['repo_url']         = $repoUrl;
        $config['copyright']        = vsprintf($this->copyrightTemplate, $copyright);
        $config['pages']            = $this->getPages($bookdown->content);

        return Yaml::dump($config);
    }

    private function getPages($bookdownContent)
    {
        if (! file_exists('doc/book/index.md')) {
            $this->symlinkReadmeToIndex();
        }
        $pages = ['index.md'];

        foreach ($this->iteratePages($bookdownContent) as $page) {
            // Regular page
            if (is_string($page) && ! preg_match('#/bookdown\.json$#', $page)) {
                $pages[] = $this->normalizePage($page);
                continue;
            }

            // Object
            if (is_object($page)) {
                foreach ((array) $page as $title => $file) {
                    if (preg_match('#\.\./README\.md$#', $file)) {
                        continue;
                    }
                    $pages[] = [$title => $this->normalizePage($file)];
                }
                continue;
            }

            // At this point, we have bookdown.json
            $page = $this->parseBookdown(getcwd() . '/doc/book', $this->normalizePage($page));
            if ($page) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    /**
     * Create the symlink doc/book/index.md pointing to repo README.md
     */
    private function symlinkReadmeToIndex()
    {
        $rootDir = getcwd();
        chdir('doc/book');
        symlink('../../README.md', 'index.md');
        chdir($rootDir);
    }

    private function iteratePages($bookdownContent)
    {
        foreach ($bookdownContent as $page) {
            if (is_object($page)) {
                yield $page;
                continue;
            }

            if (! preg_match('#\.\./README\.md$#', $page)) {
                yield $page;
                continue;
            }
        }
    }

    private function normalizePage($page, $relativePath = false)
    {
        if (! $relativePath) {
            return preg_replace('#^book/#', '', $page);
        }
        return sprintf('%s/%s', $relativePath, $page);
    }

    private function parseBookdown($basePath, $bookdownPath)
    {
        $qualifiedPath = sprintf('%s/%s', $basePath, $bookdownPath);
        if (! file_exists($qualifiedPath)) {
            return false;
        }

        $bookdown    = json_decode(file_get_contents($qualifiedPath));
        $relativePath = basename(dirname($qualifiedPath));

        $pages = [];

        foreach ($this->iteratePages($bookdown->content) as $page) {
            // Regular page
            if (is_string($page) && ! preg_match('#/bookdown\.json$#', $page)) {
                $pages[] = $this->normalizePage($page, $relativePath);
                continue;
            }

            // Object
            if (is_object($page)) {
                foreach ((array) $page as $title => $file) {
                    if (preg_match('#\.\./README\.md$#', $file)) {
                        continue;
                    }
                    $pages[] = [$title => $this->normalizePage($file, $relativePath)];
                }
                continue;
            }

            // At this point, we have bookdown.json
            $page = $this->parseBookdown(
                sprintf('%s/%s', $basePath, $relativePath),
                $this->normalizePage($page, $relativePath)
            );

            if ($page) {
                $pages[] = $page;
            }
        }

        return [
            $bookdown->title => $pages,
        ];
    }
}
