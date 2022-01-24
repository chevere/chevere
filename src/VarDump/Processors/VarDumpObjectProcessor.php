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

namespace Chevere\VarDump\Processors;

use Chevere\Str\StrBool;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Interfaces\VarDumpProcessorInterface;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\VarDump\VarDumpable;
use Chevere\VarDump\VarDumper;
use Ds\Collection;
use Ds\Set;
use Reflection;
use ReflectionObject;
use Throwable;

final class VarDumpObjectProcessor implements VarDumpProcessorInterface
{
    use ProcessorTrait;

    private object $var;

    private ReflectionObject $reflectionObject;

    private string $className;

    private Set $known;

    private int $objectId;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        $this->var = $this->varDumper->dumpable()->var();
        $this->depth = $this->varDumper->depth() + 1;
        $this->known = $this->varDumper->known();
        $this->className = $this->var::class;
        $this->handleNormalizeClassName();
        $this->objectId = spl_object_id($this->var);
        $this->info = $this->className . '#' . $this->objectId;
    }

    public function type(): string
    {
        return TypeInterface::OBJECT;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            $this->varDumper->format()
                ->highlight(
                    VarDumperInterface::CLASS_REG,
                    $this->className
                )
            .
            $this->varDumper->format()
                ->highlight(
                    VarDumperInterface::OPERATOR,
                    '#' . strval($this->objectId)
                )
        );
        if ($this->known->contains($this->objectId)) {
            $this->varDumper->writer()->write(
                ' '
                . $this->highlightOperator(
                    $this->circularReference() . ' #' . $this->objectId
                )
            );

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            $this->varDumper->writer()->write(
                ' ' .
                $this->highlightOperator($this->maxDepthReached())
            );

            return;
        }
        $this->known[] = $this->objectId;
        $this->reflectionObject = new ReflectionObject($this->var);
        if ($this->reflectionObject->implementsInterface(Collection::class)) {
            $this->varDumper->writer()->write(' ');
            (new VarDumper(
                $this->varDumper->writer(),
                $this->varDumper->format(),
                new VarDumpable($this->var->toArray())
            ))
                ->withDepth($this->depth)
                ->withIndent(
                    $this->varDumper->indent() > 1
                        ? $this->varDumper->indent() - 1
                        : $this->varDumper->indent()
                )
                ->withKnownObjects($this->known)
                ->withProcess();
        }
        $this->setProperties();
    }

    private function setProperties(): void
    {
        $properties = [];
        $reflectionClass = $this->reflectionObject;
        do {
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);

                try {
                    $value = $property->getValue($this->var);
                } catch (Throwable $e) {
                    $value = null;
                }
                $properties[$property->getName()] = [
                    $property->getName(),
                    implode(
                        ' ',
                        Reflection::getModifierNames($property->getModifiers())
                    ),
                    $value ?? null,
                ];
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());
        $keys = array_keys($properties);
        foreach ($keys as $name) {
            $el = $properties[$name];
            $this->processProperty($el[0], $el[1], $el[2]);
        }
    }

    private function processProperty(string $name, string $modifiers, $var): void
    {
        $this->varDumper->writer()->write(
            implode(' ', [
                "\n" . $this->varDumper->indentString(),
                $this->varDumper->format()->highlight(
                    VarDumperInterface::MODIFIERS,
                    $modifiers
                ),
                $this->varDumper->format()
                    ->highlight(
                        VarDumperInterface::VARIABLE,
                        '$' . $this->varDumper->format()->filterEncodedChars($name)
                    ),
                '',
            ])
        );
        (new VarDumper(
            $this->varDumper->writer(),
            $this->varDumper->format(),
            new VarDumpable($var)
        ))
            ->withDepth(
                is_scalar($var)
                ? $this->depth - 1
                : $this->depth
            )
            ->withIndent($this->varDumper->indent())
            ->withKnownObjects($this->known)
            ->withProcess();
    }

    private function handleNormalizeClassName(): void
    {
        if (
            (new StrBool($this->className))
                ->startsWith(VarDumperInterface::CLASS_ANON)
        ) {
            $this->className = VarDumperInterface::CLASS_ANON;
        }
    }
}
