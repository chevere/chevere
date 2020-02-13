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

namespace Chevere\Components\VarDump\Processors;

use Chevere\Components\Str\StrBool;
use ReflectionObject;
use Throwable;
use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\VarProcess;
use Chevere\Components\VarDump\Interfaces\VarProcessInterface;
use Reflection;
use function DeepCopy\deep_copy;

final class ObjectProcessor extends AbstractProcessor
{
    private object $var;

    private ReflectionObject $reflectionObject;

    private array $properties;

    private string $className;

    /** @var string[] An array containing object ids */
    private array $knownObjects = [];

    public function type(): string
    {
        return TypeInterface::OBJECT;
    }

    protected function process(): void
    {
        $this->var = $this->varProcess->dumpeable()->var();
        $this->knownObjects = $this->varProcess->known();
        $this->depth = $this->varProcess->depth() + 1;
        $this->className = get_class($this->var);
        $this->handleNormalizeClassName();
        $this->info = $this->className;
        $this->varProcess->writer()->write(
            $this->varProcess->formatter()->highlight(VarProcessInterface::_CLASS, $this->className)
        );
        $objectId = spl_object_id($this->var);
        if (in_array($objectId, $this->knownObjects)) {
            $this->varProcess->writer()->write(
                ' ' .
                $this->highlightOperator($this->circularReference() . ' #' . $objectId)
            );

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            $this->varProcess->writer()->write(
                ' ' .
                $this->highlightOperator($this->maxDepthReached())
            );

            return;
        }
        $this->knownObjects[] = $objectId;
        $this->reflectionObject = new ReflectionObject($this->var);
        $this->setProperties();
    }

    private function setProperties(): void
    {
        $this->properties = [];
        $reflectionObject = $this->reflectionObject;
        do {
            foreach ($reflectionObject->getProperties() as $property) {
                if (isset($this->properties[$property->getName()])) {
                    continue;
                }
                $property->setAccessible(true);
                try {
                    $value = $property->getValue($this->var);
                } catch (Throwable $e) {
                    // $e;
                }
                $this->properties[$property->getName()] = [
                    $property->getName(),
                    implode(' ', Reflection::getModifierNames($property->getModifiers())),
                    $value ?? null,
                ];
            }
        } while ($reflectionObject = $reflectionObject->getParentClass());
        $keys = array_keys($this->properties);
        foreach ($keys as $name) {
            $this->processProperty(...$this->properties[$name]);
        }
    }

    private function processProperty($name, $modifiers, $var): void
    {
        $this->varProcess->writer()->write(
            "\n" . $this->varProcess->indentString()
            . $this->varProcess->formatter()
                ->highlight(VarProcessInterface::_MODIFIERS, $modifiers)
            . ' '
            . $this->varProcess->formatter()
                ->highlight(
                    VarProcessInterface::_VARIABLE,
                    '$' . $this->varProcess->formatter()->filterEncodedChars($name)
                )
            . ' '
        );
        (new VarProcess(
            $this->varProcess->writer(),
            new VarDumpeable($var),
            $this->varProcess->formatter()
        ))
            ->withDepth(
                is_scalar($var)
                ? $this->depth - 1
                : $this->depth
            )
            ->withIndent($this->varProcess->indent() + 1)
            ->withKnownObjects($this->knownObjects)
            ->withProcessor();
    }

    private function handleNormalizeClassName(): void
    {
        if ((new StrBool($this->className))->startsWith(VarProcessInterface::_CLASS_ANON) === true) {
            $this->className = preg_replace('/[[:^print:]]/', '', $this->className);
        }
    }
}
