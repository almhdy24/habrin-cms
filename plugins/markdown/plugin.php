<?php
/**
 * Habrin CMS - Markdown Plugin
 *
 * Full-featured CommonMark rendering with Front Matter parsing,
 * tables, strikethrough, autolinks, and strict HTML sanitization.
 */

declare(strict_types=1);

use Core\Hooks;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Data\FrontMatterDataParserInterface;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;

if (!class_exists('HabrinFrontMatterParser')) {
    /**
     * Lightweight YAML front matter parser with zero external dependencies
     */
    class HabrinFrontMatterParser implements FrontMatterDataParserInterface
    {
        public function parse(string $frontMatter): mixed
        {
            $data = [];
            $lines = preg_split("/\r\n|\n|\r/", $frontMatter);
            if (!is_array($lines)) {
                return $data;
            }

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, ':')) {
                    [$k, $v] = explode(':', $line, 2);
                    $key = trim($k);
                    $val = trim($v);

                    // Strip wrapping single or double quotes
                    $len = strlen($val);
                    if ($len >= 2) {
                        $first = $val[0];
                        $last = $val[$len - 1];
                        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                            $val = substr($val, 1, -1);
                        }
                    }

                    // Cast booleans and numbers where sensible
                    if (strtolower($val) === 'true') {
                        $val = true;
                    } elseif (strtolower($val) === 'false') {
                        $val = false;
                    } elseif (is_numeric($val) && !str_starts_with($val, '0')) {
                        $val = str_contains($val, '.') ? (float) $val : (int) $val;
                    }

                    $data[$key] = $val;
                }
            }

            return $data;
        }
    }
}

// Register post_content filter
Hooks::add_filter('post_content', function (string $content, array $post = []): string {
    static $converter = null;

    if ($converter === null) {
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);

        // Core CommonMark
        $environment->addExtension(new CommonMarkCoreExtension());

        // Tables support
        $environment->addExtension(new TableExtension());

        // Strikethrough support
        $environment->addExtension(new StrikethroughExtension());

        // Autolink URLs
        if (class_exists(AutolinkExtension::class)) {
            $environment->addExtension(new AutolinkExtension());
        }

        // Front Matter extraction
        $environment->addExtension(new FrontMatterExtension(new HabrinFrontMatterParser()));

        $converter = new MarkdownConverter($environment);
    }

    $result = $converter->convert($content);

    // If front matter was detected and post array passed by reference/filter hook
    if ($result instanceof RenderedContentWithFrontMatter) {
        $frontMatter = $result->getFrontMatter();
        if (!empty($frontMatter) && is_array($frontMatter)) {
            Hooks::do_action('post_frontmatter_parsed', $frontMatter, $post);
        }
    }

    return $result->getContent();
}, 10);