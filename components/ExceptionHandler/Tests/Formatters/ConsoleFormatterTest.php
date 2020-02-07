<?php
namespace Chevere\Components\ExceptionHandler\Tests\Formatters;

use Chevere\Components\ExceptionHandler\Formatters\ConsoleFormatter;
use Chevere\Components\ExceptionHandler\Formatters\PlainFormatter;
use PHPUnit\Framework\TestCase;

final class ConsoleFormatterTest extends TestCase
{
    public function testConstruct(): void
    {
        $plainFormatter = new PlainFormatter();
        $consoleFormatter = new ConsoleFormatter();

        $array = [
            'getTraceEntryTemplate' => [],
            'getHr' => [],
            'wrapLink' => ['value'],
            'wrapSectionTitle' => ['value'],
        ];
        foreach ($array as $methodName => $args) {
            $plain = $plainFormatter->$methodName(...$args);
            $console = $consoleFormatter->$methodName(...$args);
            $this->assertTrue(strlen($plain) < strlen($console));
            $this->assertSame($plain, $this->uncolorizeConsole($console));
        }
    }

    private function uncolorizeConsole(string $string): string
    {
        return preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $string);
    }
}
