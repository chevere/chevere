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

namespace Chevere\Components\VarDump\Processors;

use ReflectionObject;
use ReflectionProperty;
use Reflector;
use Throwable;

use Chevere\Components\Path\Path;
use Chevere\Components\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\Components\VarDump\VarDump;
use Chevere\Contracts\VarDump\ProcessorContract;

use function ChevereFn\stringStartsWith;

final class ObjectProcessor implements ProcessorContract
{
    use ProcessorTrait;

    /** @var object */
    private $expression;

    /** @var VarDump */
    private $varDump;

    /** @var Reflector */
    private $reflectionObject;

    /** @var array */
    private $properties;

    /** @var string */
    private $className;

    private $aux;

    public function __construct(object $expression, VarDump $varDump)
    {
        $this->expression = $expression;
        $this->varDump = $varDump;
        $this->val = '';
        $this->parentheses = '';
        $this->reflectionObject = new ReflectionObject($expression);
        if (in_array($this->reflectionObject->getName(), $this->varDump->dontDump())) {
            $this->val .= $this->varDump->formatter()->wrap(
                VarDump::_OPERATOR,
                $this->varDump->formatter()->getEmphasis(
                    $this->reflectionObject->getName()
                )
            );

            return;
        }
        $this->setProperties();

        $this->className = get_class($expression);
        $this->handleNormalizeClassName();
        $this->parentheses = $this->className;
    }

    private function setProperties(): void
    {
        $this->properties = [];
        foreach (VarDump::PROPERTIES_REFLECTION_MAP as $visibility => $filter) {
            /** @scrutinizer ignore-call */
            $properties = $this->reflectionObject->getProperties($filter);
            foreach ($properties as $property) {
                if (!isset($this->properties[$property->getName()])) {
                    $this->setProperty($property);
                }
                $this->properties[$property->getName()]['visibility'][] = $visibility;
            }
        }
        foreach ($this->properties as $k => $v) {
            $this->processProperty($k, $v);
        }
    }

    private function setProperty(ReflectionProperty $property): void
    {
        $property->setAccessible(true);
        try {
            $value = $property->getValue($this->expression);
        } catch (Throwable $e) {
            // $e
        }
        $this->properties[$property->getName()] = ['value' => $value];
    }

    private function processProperty($key, $var): void
    {
        $visibility = implode(' ', $var['visibility'] ?? $this->properties['visibility']);
        $operator = $this->varDump->formatter()->wrap(VarDump::_OPERATOR, '->');
        $this->val .= "\n" . $this->varDump->indentString() . $this->varDump->formatter()->getEmphasis($visibility) . ' ' . $this->varDump->formatter()->getEncodedChars($key) . " $operator ";
        $this->aux = $var['value'];
        if (is_object($this->aux) && property_exists($this->aux, $key)) {
            try {
                $r = new ReflectionObject($this->aux);
                $prop = $r->getProperty($key);
                $prop->setAccessible(true);
                if ($this->aux == $prop->getValue($this->aux)) {
                    $this->val .= $this->varDump->formatter()->wrap(
                        VarDump::_OPERATOR,
                        '(' . $this->varDump->formatter()->getEmphasis('circular object reference') . ')'
                    );
                }
                return;
            } catch (Throwable $e) {
                // $e
                return;
            }
        }
        $this->handleDeepth();
    }

    private function handleDeepth(): void
    {
        if ($this->varDump->depth() < 4) {
            $new = $this->varDump
                ->respawn()
                ->withDump($this->aux, $this->varDump->indent(), $this->varDump->depth());
            $this->val .= $new->toString();
            return;
        }
        $this->val .= $this->varDump->formatter()->wrap(
            VarDump::_OPERATOR,
            '(' . $this->varDump->formatter()->getEmphasis('max depth reached') . ')'
        );
    }

    private function handleNormalizeClassName(): void
    {
        if (stringStartsWith(VarDump::ANON_CLASS, $this->className)) {
            $this->className = (new Path($this->className))->absolute();
        }
    }
}
