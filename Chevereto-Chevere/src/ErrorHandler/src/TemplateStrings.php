<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\ErrorHandler\src;

final class TemplateStrings
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
    private $i;

    /** @var string */
    private $rich;

    /** @var string */
    private $plain;

    public function __construct(Formatter $formatter)
    {
        $this->rich = '';
        $this->plain = '';
        $this->sectionsLength = count($formatter->plainContentSections);
        $this->setTitleBreak(str_repeat('=', $formatter::COLUMNS));
        $i = 0;
        foreach ($formatter->plainContentSections as $k => $plainSection) {
            $this->setPlainSection($plainSection);
            $richSection = $formatter->richContentSections[$k] ?? null;
            if ($richSection) {
                $this->setRichSection($formatter->richContentSections[$k]);
            }
            $this->process($i);
            ++$i;
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

    private function setTitleBreak(string $titleBreak): void
    {
        $this->titleBreak = $titleBreak;
    }

    private function setRichSection(array $richSection): void
    {
        $this->richSection = $richSection;
    }

    private function setPlainSection(array $plainSection): void
    {
        $this->plainSection = $plainSection;
        $this->sectionLength = count($plainSection);
    }

    private function process(int $i): void
    {
        $this->i = $i;
        $this->appendSectionWrap();
        $this->appendSectionContents();
        if ($i + 1 < $this->sectionsLength) {
            $this->appendRichSectionBreak();
            $this->appendPlainSectionBreak();
        }
    }

    private function appendSectionContents(): void
    {
        if ($this->i > 0) {
            $j = 1 == $this->sectionLength ? 0 : 1;
            for ($j; $j < $this->sectionLength; ++$j) {
                if ($this->sectionLength > 1) {
                    $this->appendEOL();
                }
                $this->rich .= '<div class="c">' . $this->richSection[$j] . '</div>';
                $this->plain .= $this->plainSection[$j];
            }
        }
    }

    private function appendSectionWrap(): void
    {
        if (0 == $this->i || isset($this->plainSection[1])) {
            $this->rich .= '<div class="t' . (0 == $this->i ? ' t--scream' : null) . '">' . $this->richSection[0] . '</div>';
            $this->plain .= html_entity_decode($this->plainSection[0]);
            if (0 == $this->i) {
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
