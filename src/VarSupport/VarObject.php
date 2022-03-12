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

namespace Chevere\VarSupport;

use Chevere\Iterator\Breadcrumb;
use Chevere\Iterator\Interfaces\BreadcrumbInterface;
use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\VarSupport\Exceptions\VarObjectNotClonableException;
use Chevere\VarSupport\Interfaces\VarObjectInterface;
use ReflectionNamedType;
use ReflectionObject;

final class VarObject implements VarObjectInterface
{
    private BreadcrumbInterface $breadcrumb;

    public function __construct(
        private object $var
    ) {
        $this->breadcrumb = new Breadcrumb();
    }

    public function var(): object
    {
        return $this->var;
    }

    /**
     * @throws VarObjectNotClonableException
     */
    public function assertClonable(): void
    {
        $this->assertVarClonable($this->var);
    }

    private function assertVarClonable(mixed $var): void
    {
        if (is_object($var)) {
            $this->breadcrumbObject($var);
        } elseif (is_iterable($var)) {
            $this->breadcrumbIterable($var);
        }
    }

    /**
     * @param iterable<mixed, mixed> $var
     * @throws VarObjectNotClonableException
     * @throws OutOfBoundsException
     */
    private function breadcrumbIterable(iterable $var): void
    {
        $this->breadcrumb = $this->breadcrumb->withAddedItem('(iterable)');
        $iterableKey = $this->breadcrumb->pos();
        foreach ($var as $key => $val) {
            $key = strval($key);
            $this->breadcrumb = $this->breadcrumb
                ->withAddedItem('key: ' . $key);
            $memberKey = $this->breadcrumb->pos();
            $this->assertVarClonable($val);
            $this->breadcrumb = $this->breadcrumb
                ->withRemovedItem($memberKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemovedItem($iterableKey);
    }

    private function breadcrumbObject(object $var): void
    {
        $this->breadcrumb = $this->breadcrumb
            ->withAddedItem('object: ' . $var::class);
        $objectKey = $this->breadcrumb->pos();
        $reflection = new ReflectionObject($var);
        if (!$reflection->isCloneable()) {
            throw new VarObjectNotClonableException(
                message: (new Message('Object is not clonable at %at%'))
                    ->code('%at%', $this->breadcrumb->__toString())
            );
        }
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            /** @var ?ReflectionNamedType $namedType */
            $namedType = $property->getType();
            $propertyType = $namedType !== null
                ? $namedType->getName() . ' '
                : '';
            $this->breadcrumb = $this->breadcrumb
                ->withAddedItem(
                    'property: '
                    . $propertyType
                    . '$' . $property->getName()
                );
            $propertyKey = $this->breadcrumb->pos();
            // @infection-ignore-all
            $property->setAccessible(true);
            if ($property->isInitialized($var)) {
                $this->assertVarClonable($property->getValue($var));
            }
            $this->breadcrumb = $this->breadcrumb
                ->withRemovedItem($propertyKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemovedItem($objectKey);
    }
}
