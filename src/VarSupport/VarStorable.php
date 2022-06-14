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
use Chevere\VarSupport\Exceptions\VarStorableException;
use Chevere\VarSupport\Interfaces\VarStorableInterface;
use ReflectionNamedType;
use ReflectionObject;

final class VarStorable implements VarStorableInterface
{
    private BreadcrumbInterface $breadcrumb;

    public function __construct(
        private mixed $var
    ) {
        $this->breadcrumb = new Breadcrumb();
        $this->assertExportable($this->var);
    }

    public function var(): mixed
    {
        return $this->var;
    }

    public function toExport(): string
    {
        return var_export($this->var, true);
    }

    public function toSerialize(): string
    {
        return serialize($this->var);
    }

    private function assertExportable(mixed $var): void
    {
        $this->assertIsNotResource($var);
        if (is_object($var)) {
            $this->breadcrumbObject($var);
        } elseif (is_iterable($var)) {
            $this->breadcrumbIterable($var);
        }
    }

    /**
     * @throws VarStorableException
     */
    private function assertIsNotResource(mixed $var): void
    {
        if (is_resource($var)) {
            $message = $this->breadcrumb->count() > 0
                ? (new Message("Argument contains a resource at %at%"))
                    ->withCode('%at%', $this->breadcrumb->__toString())
                : new Message("Argument is of type resource.");

            throw new VarStorableException(message: $message);
        }
    }

    /**
     * @param iterable<mixed, mixed> $var
     * @throws VarStorableException
     * @throws OutOfBoundsException
     */
    private function breadcrumbIterable(iterable $var): void
    {
        $this->breadcrumb = $this->breadcrumb->withAdded('(iterable)');
        $iterableKey = $this->breadcrumb->pos();
        foreach ($var as $key => $val) {
            $key = strval($key);
            $this->breadcrumb = $this->breadcrumb
                ->withAdded('key: ' . $key);
            $memberKey = $this->breadcrumb->pos();
            $this->assertExportable($val);
            $this->breadcrumb = $this->breadcrumb
                ->withRemoved($memberKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemoved($iterableKey);
    }

    private function breadcrumbObject(object $var): void
    {
        $this->breadcrumb = $this->breadcrumb
            ->withAdded('object: ' . $var::class);
        $objectKey = $this->breadcrumb->pos();
        $reflection = new ReflectionObject($var);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            /** @var ?ReflectionNamedType $namedType */
            $namedType = $property->getType();
            $propertyType = $namedType !== null
                ? $namedType->getName() . ' '
                : '';
            $this->breadcrumb = $this->breadcrumb
                ->withAdded(
                    'property: '
                    . $propertyType
                    . '$' . $property->getName()
                );
            $propertyKey = $this->breadcrumb->pos();
            // @infection-ignore-all
            $property->setAccessible(true);
            if ($property->isInitialized($var)) {
                $this->assertExportable($property->getValue($var));
            }
            $this->breadcrumb = $this->breadcrumb
                ->withRemoved($propertyKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemoved($objectKey);
    }
}
