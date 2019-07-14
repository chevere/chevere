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

namespace Chevereto\Chevere\ErrorHandler;

class TemplateStrings
{
    /** @var string */
    protected $titleBreak;

    /** @var array */
    protected $richSection;

    /** @var array */
    protected $plainSection;

    /** @var int */
    protected $sectionLength;

    /** @var int */
    protected $sectionsLength;

    /** @var string */
    public $rich;

    /** @var string */
    public $plain;

    public function __construct(Formatter $formatter)
    {
        $this->rich = '';
        $this->plain = '';
        $this->sectionsLength = count($formatter->plainContentSections);
    }

    public function setTitleBreak(string $titleBreak)
    {
        $this->titleBreak = $titleBreak;
    }

    public function setRichSection(?array $richSection): self
    {
        $this->richSection = $richSection;

        return $this;
    }

    public function setPlainSection(?array $plainSection): self
    {
        $this->plainSection = $plainSection;
        $this->sectionLength = count($plainSection);

        return $this;
    }

    public function process(int $i)
    {
        $this->appendSection($i, $this->rich, $this->plain, $this->richSection, $this->plainSection, $this->titleBreak);
        $this->appendSectionContent($i, $this->rich, $this->plain, $this->richSection, $this->plainSection, $this->sectionLength);
        if ($i + 1 < $this->sectionsLength) {
            $this->appendRichSectionBreak($this->rich);
            $this->appendPlainSectionBreak($this->plain);
        }
    }

    protected function appendSectionContent(int $i, string &$rich, string &$plain, ?array $richSection, array $plainSection, int $sectionLength)
    {
        if ($i > 0) {
            $j = 1 == $sectionLength ? 0 : 1;
            for ($j; $j < $sectionLength; ++$j) {
                if ($sectionLength > 1) {
                    $this->appendEOL($rich);
                    $this->appendEOL($plain);
                }
                $rich .= '<div class="c">'.$richSection[$j].'</div>';
                $plain .= $plainSection[$j];
            }
        }
    }

    protected function appendSection(int $i, string &$rich, string &$plain, ?array $richSection, array $plainSection, string $titleBreak)
    {
        if (0 == $i || isset($plainSection[1])) {
            $rich .= '<div class="t'.(0 == $i ? ' t--scream' : null).'">'.$richSection[0].'</div>';
            $plain .= html_entity_decode($plainSection[0]);
            if (0 == $i) {
                $this->appendRichTitleBreak($rich, $titleBreak);
                $this->appendPlainTitleBreak($plain, $titleBreak);
            }
        }
    }

    protected function appendRichTitleBreak(string &$rich, string $break)
    {
        $rich .= "\n".'<div class="hide">'.$break.'</div>';
    }

    protected function appendPlainTitleBreak(string &$plain, string $break)
    {
        $plain .= "\n".$break;
    }

    protected function appendEOL(string &$template)
    {
        $template .= "\n";
    }

    protected function appendRichSectionBreak(string &$rich)
    {
        $rich .= "\n".'<br>'."\n";
    }

    protected function appendPlainSectionBreak(string &$plain)
    {
        $plain .= "\n\n";
    }
}
