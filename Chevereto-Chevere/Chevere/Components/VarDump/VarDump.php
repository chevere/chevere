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

use BadMethodCallException;
use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Processors\ScalarProcessor;
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

    private bool $hasVar = false;

    private string $val = '';

    private string $type;

    private string $info;

    private string $template;

    /**
     * Creates a new instance.
     *
     * @param FormatterInterface $formatter A VarDump formatter
     */
    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
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
    public function withVar($var): VarDumpInterface
    {
        $new = clone $this;
        ++$new->depth;
        $new->var = $var;
        $new->hasVar = true;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVar(): bool
    {
        return $this->hasVar;
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        return $this->var;
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
        if (!$this->hasVar) {
            throw new BadMethodCallException('This method cannot be called without a $var');
        }
        $this->type = varType($this->var);
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
        switch ($this->type) {
            case static::TYPE_BOOLEAN:
                $processor = new BooleanProcessor($this);
                break;
            case static::TYPE_ARRAY:
                ++$this->indent;
                $processor = new ArrayProcessor($this);
                break;
            case static::TYPE_OBJECT:
                ++$this->indent;
                $processor = new ObjectProcessor($this);
                break;
            default:
                $processor = new ScalarProcessor($this);
                break;
        }

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
            case static::TYPE_ARRAY:
            case static::TYPE_OBJECT:
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
