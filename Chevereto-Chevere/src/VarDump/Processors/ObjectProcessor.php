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

namespace Chevere\VarDump\Processors;

use ReflectionObject;
use Reflector;
use Throwable;
use Chevere\Contracts\VarDump\ProcessorContract;
use Chevere\Path\Path;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\VarDump\VarDump;

use function ChevereFn\pathNormalize;
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
        foreach ($this->properties as $k => $v) {
            $this->processProperty($k, $v);
        }
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
                    $property->setAccessible(true);
                    try {
                        $value = $property->getValue($this->expression);
                    } catch (Throwable $e) {
                        // $e
                    }
                    $this->properties[$property->getName()] = ['value' => $value];
                }
                $this->properties[$property->getName()]['visibility'][] = $visibility;
            }
        }
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
                $p = $r->getProperty($key);
                $p->setAccessible(true);
                if ($this->aux == $p->getValue($this->aux)) {
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
        } else {
            $this->val .= $this->varDump->formatter()->wrap(
                VarDump::_OPERATOR,
                '(' . $this->varDump->formatter()->getEmphasis('max depth reached') . ')'
            );
        }
    }

    private function handleNormalizeClassName(): void
    {
        if (stringStartsWith(VarDump::ANON_CLASS, $this->className)) {
            $this->className = pathNormalize($this->className);
        }
    }
}
