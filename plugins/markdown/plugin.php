<?php
/**
 * Pico JSON CMS - Markdown Plugin
 */

declare(strict_types=1);

use Core\Hooks;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;

// Register filter using new Hooks class
Hooks::add_filter('post_content', function (string $content, array $post): string {

    static $converter = null;

    if ($converter === null) {
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);

        // Core Markdown
        $environment->addExtension(new CommonMarkCoreExtension());

        // Extra features
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new StrikethroughExtension());

        $converter = new MarkdownConverter($environment);
    }

    return $converter->convert($content)->getContent();
});