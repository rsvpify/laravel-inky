<?php

namespace Rsvpify\LaravelInky;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\CompilerInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class InkyCompilerEngine extends CompilerEngine
{
    protected $filesystem;

    public function __construct(CompilerInterface $compiler, Filesystem $filesystem)
    {
        parent::__construct($compiler);

        $this->filesystem = $filesystem;
    }

    public function get($inkyFilePath, array $data = [])
    {
        // Compiles the inky template as if it were a regular blade file
        $html = parent::get($inkyFilePath, $data);

        // remove css stylesheet links from email's HTML
        $crawler = new Crawler;
        $crawler->addHtmlContent($html);
        $cssLinks = $crawler->filter('link[rel=stylesheet]');

        $cssLinks->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $htmlWithoutLinks = $crawler->html();

        // Combine all stylesheets into 1 string of CSS
        $combinedStyles = collect(config('inky.stylesheets'))->map(function ($path) {
            return $this->filesystem->get(base_path($path));
        })->implode("\n\n");

        $inliner = new CssToInlineStyles;

        return $inliner->convert(
            $this->appendExternalMediaQueries($htmlWithoutLinks, $combinedStyles),
            $combinedStyles
        );
    }

    public function getFiles()
    {
        return $this->filesystem;
    }

    protected function appendExternalMediaQueries(string $html, string $css): string
    {
        if (($mediaQueries = $this->extractMediaQueries($css)) === '') {
            return $html;
        }

        $styleTag = "<style>\n{$mediaQueries}\n</style>";

        if (Str::contains($html, '</head>')) {
            return Str::replaceFirst('</head>', "{$styleTag}\n</head>", $html);
        }

        return "{$styleTag}\n{$html}";
    }

    protected function extractMediaQueries(string $css): string
    {
        return Str::of($css)
            ->matchAll('/@media[^{]*+{(?:[^{}]++|{[^{}]*+})*+}/i')
            ->map(fn ($query) => trim($query))
            ->filter()
            ->implode("\n\n");
    }
}
