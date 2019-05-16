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

namespace Chevereto\Chevere\Utils;

/**
 * Another var_dump replacement.
 * FIXME: Doesn't work with BetterReflection objects.
 */
class Dump extends DumpAbstract
{
    /** @var string */
    protected $output;

    /** @var string */
    protected $template;

    /** @var string */
    protected $parentheses;

    /** @var mixed */
    protected $val;

    /** @var string */
    protected $className;

    protected function setClassName(string $className): void
    {
        $this->className = $className;
    }

    protected function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    protected function appendVal($val): void
    {
        $this->val .= $val;
    }

    protected function setParentheses(string $parentheses): void
    {
        $this->parentheses = $parentheses;
    }

    protected function setOutput(string $output): void
    {
        $this->output = $output;
    }

    public function __toString(): string
    {
        return $this->output;
    }

    /**
     * Dumps information about a variable.
     *
     * @param mixed $var      anything
     * @param int   $indent   left padding (spaces) for this entry
     * @param array $dontDump array containing classes that won't get dumped
     *
     * @return string parsed dump string
     */
    public static function out($var, int $indent = null, array $dontDump = [], int $depth = 0): string
    {
        return (string) new static(...func_get_args());
    }
}
