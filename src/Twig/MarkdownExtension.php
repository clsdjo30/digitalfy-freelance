<?php

namespace App\Twig;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    private GithubFlavoredMarkdownConverter $converter;

    public function __construct()
    {
        // Configuration Markdown avec GitHub Flavored Markdown (inclut les tableaux)
        $config = [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'table' => [
                'wrap' => [
                    'enabled' => false,
                ],
            ],
        ];

        $this->converter = new GithubFlavoredMarkdownConverter($config);
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'convertMarkdown'], ['is_safe' => ['html']]),
        ];
    }

    public function convertMarkdown(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        return $this->converter->convert($content)->getContent();
    }
}
