<?php

namespace Whojinn\Test;

require __DIR__ . '/../vendor/autoload.php';

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;
use Whojinn\Danraku\DanrakuExtension;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFileEquals;
use function PHPUnit\Framework\assertFileExists;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringEqualsFile;

final class DanrakuTest extends TestCase
{
    public array $config;

    public $environment;

    /**
     * テストに必要な前処理を共通化させる。
     */
    private function testTemplate(string $markdown_path, string $otehon_path): array
    {

        $template_path = __DIR__ . '/data/';

        $this->environment->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new GithubFlavoredMarkdownExtension())
            ->addExtension(new AttributesExtension())
            ->addExtension(new FootnoteExtension())
            ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($this->environment);

        $test = $converter->convertToHtml(file_get_contents($template_path . $markdown_path));

        return [
            "markdown" => $test,
            "otehon" => file_get_contents($template_path . $otehon_path),
        ];
    }

    protected function setUp(): void
    {
        $this->config = [
            'danraku' => [
                'ignore_alphabet' => false,
                'ignore_footnote' => false,
            ]
        ];

        clearstatcache();

        $this->environment = new Environment($this->config);
    }

    final public function testDanrakuNormal(): void
    {
        $test_data = $this->testTemplate('paragraph.md', 'paragraph.html');

        // assertFileEquals($test_data["otehon"], $test_data["markdown"], "基本テストがうまくいかなかったでござる");
        assertEquals($test_data["otehon"], $test_data["markdown"], "基本テストがうまくいかなかったでござる");
    }

    final public function testDanrakuAttribute(): void
    {
        $test_data = $this->testTemplate('attribute.md', 'attribute.html');


        assertEquals($test_data["otehon"], $test_data["markdown"], "属性テストがうまくいかなかったでござる");
    }

    final public function testDanrakuIgnoreAlphabet(): void
    {
        $this->environment->mergeConfig([
            'danraku' => [
                'ignore_alphabet' => true,
            ]
        ]);

        $test_data = $this->testTemplate('ignore_alphabet.md', 'ignore_alphabet.html');

        assertEquals($test_data["otehon"], $test_data["markdown"], "アルファベット無視機能「オン」テストがうまくいかなかったでござる");
    }

    final public function testDanrakuOffIgnoreAlphabet(): void
    {
        $this->environment->mergeConfig([
            'danraku' => [
                'ignore_alphabet' => false,
            ]
        ]);

        $test_data = $this->testTemplate('ignore_alphabet_off.md', 'ignore_alphabet_off.html');

        assertEquals($test_data["otehon"], $test_data["markdown"], "アルファベット無視機能「オフ」のテストがうまくいかなかったでござる");
    }

    final public function testDanrakuIgnoreFootnote(): void
    {
        $this->environment->mergeConfig([
            'danraku' => [
                'ignore_footnote' => false,
            ]
        ]);

        $test_data = $this->testTemplate('ignore_footnote.md', 'ignore_footnote.html');

        assertEquals($test_data["otehon"], $test_data["markdown"], "脚注無視機能「オン」テストがうまくいかなかったでござる");
    }

    final public function testDanrakuOffIgnoreFootnote(): void
    {
        $this->environment->mergeConfig([
            'danraku' => [
                'ignore_footnote' => false,
            ]
        ]);

        $test_data = $this->testTemplate('ignore_footnote.md', 'ignore_footnote.html');

        assertEquals($test_data["otehon"], $test_data["markdown"], "脚注無視機能「オフ」テストがうまくいかなかったでござる");
    }

    final public function testDanrakuEscape(): void
    {
        $test_data = $this->testTemplate('escape.md', 'escape.html');

        assertEquals($test_data["otehon"], $test_data["markdown"], "エスケープテストがうまくいかなかったでござる");
    }
}
