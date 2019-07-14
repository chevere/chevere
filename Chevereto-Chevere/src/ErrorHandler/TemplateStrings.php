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

    /** @var int */
    protected $i;

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
        $this->i = $i;
        $this->appendSectionWrap();
        $this->appendSectionContents();
        if ($i + 1 < $this->sectionsLength) {
            $this->appendRichSectionBreak();
            $this->appendPlainSectionBreak();
        }
    }

    protected function appendSectionContents()
    {
        if ($this->i > 0) {
            $j = 1 == $this->sectionLength ? 0 : 1;
            for ($j; $j < $this->sectionLength; ++$j) {
                if ($this->sectionLength > 1) {
                    $this->appendEOL();
                }
                $this->rich .= '<div class="c">'.$this->richSection[$j].'</div>';
                $this->plain .= $this->plainSection[$j];
            }
        }
    }

    protected function appendSectionWrap()
    {
        if (0 == $this->i || isset($this->plainSection[1])) {
            $this->rich .= '<div class="t'.(0 == $this->i ? ' t--scream' : null).'">'.$this->richSection[0].'</div>';
            $this->plain .= html_entity_decode($this->plainSection[0]);
            if (0 == $this->i) {
                $this->appendRichTitleBreak($this->rich, $this->titleBreak);
                $this->appendPlainTitleBreak($this->plain, $this->titleBreak);
            }
        }
    }

    protected function appendRichTitleBreak()
    {
        $this->rich .= "\n".'<div class="hide">'.$this->titleBreak.'</div>';
    }

    protected function appendPlainTitleBreak()
    {
        $this->plain .= "\n".$this->titleBreak;
    }

    protected function appendEOL()
    {
        $this->rich .= "\n";
        $this->plain .= "\n";
    }

    protected function appendRichSectionBreak()
    {
        $this->rich .= "\n".'<br>'."\n";
    }

    protected function appendPlainSectionBreak()
    {
        $this->plain .= "\n\n";
    }
}
