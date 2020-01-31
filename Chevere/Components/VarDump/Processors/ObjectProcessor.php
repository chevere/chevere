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

use Chevere\Components\Filesystem\Path\Path;
use ReflectionObject;
use ReflectionProperty;
use Throwable;
use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\VarFormat;
use Chevere\Components\VarDump\Interfaces\VarFormatInterface;
use function ChevereFn\stringStartsWith;

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
        $this->var = $this->varFormat->dumpeable()->var();
        $this->knownObjects = $this->varFormat->known();
        $this->depth = $this->varFormat->depth() + 1;
        $this->className = get_class($this->var);
        $this->handleNormalizeClassName();
        $this->info = $this->className;
        $objectId = spl_object_id($this->var);
        if (in_array($objectId, $this->knownObjects)) {
            $this->value .= $this->highlightOperator($this->circularReference() . ' #' . $objectId);

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            $this->value .= $this->highlightOperator($this->maxDepthReached());

            return;
        }
        $this->knownObjects[] = $objectId;
        $this->reflectionObject = new ReflectionObject($this->var);
        $this->setProperties();
    }

    private function setProperties(): void
    {
        $this->properties = [];
        foreach (VarFormatInterface::PROPERTIES_REFLECTION_MAP as $visibility => $filter) {
            /** @scrutinizer ignore-call */
            $properties = $this->reflectionObject->getProperties($filter);
            foreach ($properties as $property) {
                if (!isset($this->properties[$property->getName()])) {
                    $this->setProperty($property);
                }
                $this->properties[$property->getName()]['visibility'][] = $visibility;
            }
        }
        foreach ($this->properties as $name => $value) {
            $this->processProperty($name, $value);
        }
    }

    private function setProperty(ReflectionProperty $property): void
    {
        $property->setAccessible(true);
        try {
            $value = $property->getValue($this->var);
        } catch (Throwable $e) {
            // $e;
        }
        $this->properties[$property->getName()] = ['value' => $value ?? null];
    }

    private function processProperty($name, $value): void
    {
        $visibility = implode(' ', $value['visibility'] ?? $this->properties['visibility']);
        $wrappedVisibility = $this->varFormat->formatter()->highlight(VarFormatInterface::_PRIVACY, $visibility);
        $property = '$' . $this->varFormat->formatter()->filterEncodedChars($name);
        $wrappedProperty = $this->varFormat->formatter()->highlight(VarFormatInterface::_VARIABLE, $property);
        $this->value .= "\n" . $this->varFormat->indentString() . $wrappedVisibility . ' ' . $wrappedProperty . ' ';
        $this->handleDepth($value['value']);
    }

    private function handleDepth($var): void
    {
        $deep = $this->depth;
        if (is_scalar($var)) {
            $deep -= 1;
        }
        // $deep = is_object($var) || is_iterable($var) ? $this->depth : $this->depth - 1;
        $varFormat = (new VarFormat(new VarDumpeable($var), $this->varFormat->formatter()))
            ->withDepth($deep)
            ->withIndent($this->varFormat->indent() + 1)
            ->withKnownObjects($this->knownObjects)
            ->withProcess();
        $this->value .= $varFormat->toString();
    }

    private function handleNormalizeClassName(): void
    {
        if (stringStartsWith(VarFormatInterface::_CLASS_ANON, $this->className)) {
            $this->className = preg_replace('/[[:^print:]]/', '', $this->className);
        }
    }
}
