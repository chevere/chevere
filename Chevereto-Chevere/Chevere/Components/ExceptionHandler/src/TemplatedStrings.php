<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\ExceptionHandler\src;

/**
 * Generates template strings for Output.
 */
final class TemplatedStrings
{
    /** @var string */
    private $titleBreak;

    /** @var array */
    private $richSection;

    /** @var array */
    private $plainSection;

    /** @var int */
    private $sectionLength;

    /** @var int */
    private $sectionsLength;

    /** @var int */
    private $count;

    /** @var string */
    private $rich;

    /** @var string */
    private $plain;

    public function __construct(Formatter $formatter)
    {
        $this->rich = '';
        $this->plain = '';
        $this->sectionsLength = count($formatter->plainContentSections());
        $this->titleBreak = str_repeat('=', $formatter::COLUMNS);
        $this->count = 0;
        foreach ($formatter->plainContentSections() as $k => $plainSection) {
            $this->plainSection = $plainSection;
            $this->sectionLength = count($plainSection);
            $richSection = $formatter->richContentSections()[$k] ?? null;
            if ($richSection) {
                $this->richSection = $richSection;
            }
            $this->process();
            ++$this->count;
        }
    }

    public function rich(): string
    {
        return $this->rich;
    }

    public function plain(): string
    {
        return $this->plain;
    }

    private function process(): void
    {
        $this->appendSectionWrap();
        $this->appendSectionContents();
        if ($this->count + 1 < $this->sectionsLength) {
            $this->appendRichSectionBreak();
            $this->appendPlainSectionBreak();
        }
    }

    private function appendSectionContents(): void
    {
        if ($this->count > 0) {
            $key = 1 == $this->sectionLength ? 0 : 1;
            for ($key; $key < $this->sectionLength; ++$key) {
                if ($this->sectionLength > 1) {
                    $this->appendEOL();
                }
                $this->rich .= '<div class="c">' . $this->richSection[$key] . '</div>';
                $this->plain .= $this->plainSection[$key];
            }
        }
    }

    private function appendSectionWrap(): void
    {
        if (0 == $this->count || isset($this->plainSection[1])) {
            $this->rich .= '<div class="t' . (0 == $this->count ? ' t--scream' : null) . '">' . $this->richSection[0] . '</div>';
            $this->plain .= html_entity_decode($this->plainSection[0]);
            if (0 == $this->count) {
                $this->appendRichTitleBreak();
                $this->appendPlainTitleBreak();
            }
        }
    }

    private function appendRichTitleBreak(): void
    {
        $this->rich .= "\n" . '<div class="hide">' . $this->titleBreak . '</div>';
    }

    private function appendPlainTitleBreak(): void
    {
        $this->plain .= "\n" . $this->titleBreak;
    }

    private function appendEOL(): void
    {
        $this->rich .= "\n";
        $this->plain .= "\n";
    }

    private function appendRichSectionBreak(): void
    {
        $this->rich .= "\n" . '<br>' . "\n";
    }

    private function appendPlainSectionBreak(): void
    {
        $this->plain .= "\n\n";
    }
}
