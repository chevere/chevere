<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\ExceptionHandler\Documents;

use Chevere\Components\ExceptionHandler\Formatters\ConsoleFormatter;
use Chevere\Components\ExceptionHandler\Interfaces\DocumentInterface;
use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleDocument extends AbstractDocument
{
    private int $verbosity = 0;

    /**
     * {@inheritdoc}
     */
    public function getFormatter(): FormatterInterface
    {
        return new ConsoleFormatter;
    }

    public function getSectionsTemplate(): array
    {
        $consoleColor = new ConsoleColor;
        $linkColor = fn (string $message) => $consoleColor->apply(['underline', 'blue'], $message);
        $sectionColor = fn (string $message) => $consoleColor->apply('green', $message);
        $title = $consoleColor->apply(['red', 'bold'], '%title% in ');
        $title .= $linkColor('%fileLine%');

        return [
            static::SECTION_TITLE => $title,
            static::SECTION_MESSAGE => $sectionColor('# Message') . "\n" . '%message% %codeWrap%',
            static::SECTION_ID => $sectionColor('# Incident ID:%id%') . "\n" . 'Logged at ' . $linkColor('%logFilename%'),
            static::SECTION_TIME => $sectionColor('# Time') . "\n" . '%dateTimeUtcAtom% [%timestamp%]',
            static::SECTION_STACK => $sectionColor('# Stack trace') . "\n" . '%stack%',
            static::SECTION_CLIENT => $sectionColor('# Client') . "\n" . '%clientIp% %clientUserAgent%',
            static::SECTION_REQUEST => $sectionColor('# Request') . "\n" . '%serverProtocol% %requestMethod% %uri%',
            static::SECTION_SERVER => $sectionColor('# Server') . "\n" . '%phpUname%',
        ];
    }

    /**
     * @return array string[]
     */
    public function getSectionsVerbosity(): array
    {
        return [
            0 => OutputInterface::VERBOSITY_QUIET,
            1 => OutputInterface::VERBOSITY_QUIET,
            2 => OutputInterface::VERBOSITY_QUIET,
            3 => OutputInterface::VERBOSITY_VERBOSE,
            4 => OutputInterface::VERBOSITY_VERY_VERBOSE,
            5 => OutputInterface::VERBOSITY_VERBOSE,
        ];
    }

    /**
     * Return an instance with the specified verbosity.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified verbosity.
     *
     * Calling this method will reset the document sections to fit the target verbosity.
     */
    public function withVerbosity(int $verbosity): ConsoleDocument
    {
        $new = clone $this;
        $new->verbosity = $verbosity;
        $new->sections = $new->getSections();
        $new->sectionsTemplate = $new->getSectionsTemplate();
        $new->handleVerbositySections();

        return $new;
    }

    /**
     * Provides access to the instance verbosity.
     */
    public function verbosity(): int
    {
        return $this->verbosity;
    }

    private function handleVerbositySections(): void
    {
        $sectionsVerbosity = $this->getSectionsVerbosity();
        foreach ($this->sections as $pos => $sectionName) {
            $verbosityLevel = $sectionsVerbosity[$pos] ?? 0;
            if ($this->verbosity < $verbosityLevel) {
                $key = array_search($sectionName, $this->sections);
                unset($this->sections[$key]);
            }
        }
    }
}
