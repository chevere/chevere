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

use Chevere\Components\Path\Path;
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

    private $aux;

    private array $known = [];

    public function type(): string
    {
        return TypeInterface::OBJECT;
    }

    protected function process(): void
    {
        $this->var = $this->varFormat->dumpeable()->var();
        $this->known = $this->varFormat->known();
        $this->depth = $this->varFormat->depth() + 1;
        $this->className = get_class($this->var);
        $this->info = $this->className;
        $objectId = spl_object_id($this->var);
        if (in_array($objectId, $this->known)) {
            $this->val .= $this->varFormat->formatter()->highlight(
                VarFormatInterface::_OPERATOR,
                '*circular object reference*'
            );

            return;
        } else {
            $this->known[] = $objectId;
        }
        if ($this->depth >= 4) {
            $this->val .= $this->varFormat->formatter()->highlight(
                VarFormatInterface::_OPERATOR,
                '*max depth reached*'
            );

            return;
        }
        $this->reflectionObject = new ReflectionObject($this->var);
        $this->setProperties();
        $this->handleNormalizeClassName();
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
            if (!$this->processProperty($name, $value)) {
                break;
            }
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

    private function processProperty($name, $value): bool
    {
        $visibility = implode(' ', $value['visibility'] ?? $this->properties['visibility']);
        $wrappedVisibility = $this->varFormat->formatter()->highlight(VarFormatInterface::_PRIVACY, $visibility);
        $property = '$' . $this->varFormat->formatter()->filterEncodedChars($name);
        $wrappedProperty = $this->varFormat->formatter()->highlight(VarFormatInterface::_VARIABLE, $property);
        $this->val .= "\n" . $this->varFormat->indentString() . $wrappedVisibility . ' ' . $wrappedProperty . ' ';
        $this->aux = $value['value'];

        return $this->handleDeepth($this->aux);
    }

    private function handleDeepth(): bool
    {
        $new = (new VarFormat(new VarDumpeable($this->aux), $this->varFormat->formatter()))
                ->withIndent($this->varFormat->indent() + 1)
                ->withDepth($this->depth)
                ->withKnown($this->known)
                ->withProcess();
        $this->val .= $new->toString();

        return true;
    }

    private function handleNormalizeClassName(): void
    {
        if (stringStartsWith(VarFormatInterface::_CLASS_ANON, $this->className)) {
            $this->className = preg_replace('/[[:^print:]]/', '', $this->className);
        }
    }
}
