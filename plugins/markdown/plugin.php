<?php
/**
 * Pico JSON CMS - Markdown Plugin
 */

declare(strict_types=1);

use Core\Hooks;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;

// Register filter using new Hooks class
Hooks::add_filter('post_content', function (string $content, array $post): string {

    static $converter = null;

    if ($converter === null) {

        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // Core Markdown
        $environment->addExtension(new CommonMarkCoreExtension());

        // Extra features
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new StrikethroughExtension());

        $converter = new CommonMarkConverter([], $environment);
    }

    return $converter->convert($content)->getContent();
});