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

namespace Chevere\Components\VarDump;

use LogicException;
use Chevere\Components\Message\Message;
use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use function ChevereFn\varType;

/**
 * Analyze a variable and provide a formated string representation of its type and data.
 */
final class VarDump implements VarDumpInterface
{
    private FormatterInterface $formatter;

    /** @var array [className,] */
    private array $dontDump = [];

    private string $output = '';

    private int $indent = 0;

    private string $indentString = '';

    private int $depth = 0;

    private $var;

    private string $val = '';

    private string $type;

    private string $info;

    private string $template;

    /**
     * Creates a new instance.
     *
     * @param FormatterInterface $formatter A VarDump formatter
     */
    public function __construct($var, FormatterInterface $formatter)
    {
        $this->var = $var;
        $this->type = varType($this->var);
        $this->formatter = $formatter;
        ++$this->depth;
    }

    /**
     * {@inheritdoc}
     */
    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function withDontDump(string ...$dontDump): VarDumpInterface
    {
        $new = clone $this;
        $new->dontDump = $dontDump;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function dontDump(): array
    {
        return $this->dontDump;
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        return $this->var;
    }

    public function type(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function withIndent(int $indent): VarDumpInterface
    {
        $new = clone $this;
        $new->indent = $indent;
        $new->indentString = $new->formatter
            ->getIndent($indent);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function indent(): int
    {
        return $this->indent;
    }

    /**
     * {@inheritdoc}
     */
    public function withDepth(int $depth): VarDumpInterface
    {
        $new = clone $this;
        $new->depth = $depth;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function depth(): int
    {
        return $this->depth;
    }

    /**
     * {@inheritdoc}
     */
    public function process(): VarDumpInterface
    {
        $this->handleType();
        $this->setTemplate();
        $this->setOutput();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function indentString(): string
    {
        return $this->indentString;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->output;
    }

    private function handleType(): void
    {
        $processor = static::PROCESSORS[$this->type] ?? null;
        if (!isset($processor)) {
            throw new LogicException(
                (new Message('No processor for type %type%'))
                    ->code('%type%', $this->type)
                    ->toString()
            );
        }
        if (in_array($this->type, [TypeInterface::ARRAY, TypeInterface::OBJECT])) {
            ++$this->indent;
        }
        $processor = new $processor($this);
        $this->val .= $processor->val();
        $this->info = $processor->info();
        $this->handleInfo();
    }

    private function handleInfo(): void
    {
        if ('' !== $this->info) {
            if (strpos($this->info, '=')) {
                $this->info = $this->formatter->applyEmphasis("($this->info)");
            } else {
                $this->info = $this->formatter->applyWrap(static::_CLASS, $this->info);
            }
        }
    }

    private function setTemplate(): void
    {
        switch ($this->type) {
            case TypeInterface::ARRAY:
            case TypeInterface::OBJECT:
                $this->template = '%type% %info% %val%';
                break;
            default:
                $this->template = '%type% %val% %info%';
                break;
        }
    }

    private function setOutput(): void
    {
        $message = $this->template;
        foreach (['info', 'val'] as $property) {
            if ('' == $this->$property) {
                $message = str_replace('%' . $property . '%', null, $message);
                $message = preg_replace('!\s+!', ' ', $message);
                $message = trim($message);
            }
        }
        $this->output = strtr($message, [
            '%type%' => $this->formatter->applyWrap($this->type, $this->type),
            '%val%' => $this->val,
            '%info%' => $this->info,
        ]);
    }
}
