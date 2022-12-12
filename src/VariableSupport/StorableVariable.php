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

namespace Chevere\VariableSupport;

use Chevere\Iterator\Breadcrumb;
use Chevere\Iterator\Interfaces\BreadcrumbInterface;
use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\VariableSupport\Exceptions\UnableToStoreException;
use Chevere\VariableSupport\Interfaces\StorableVariableInterface;
use ReflectionNamedType;
use ReflectionObject;
use Symfony\Component\VarExporter\VarExporter;

final class StorableVariable implements StorableVariableInterface
{
    private BreadcrumbInterface $breadcrumb;

    public function __construct(
        private mixed $variable
    ) {
        $this->breadcrumb = new Breadcrumb();
    }

    public function variable(): mixed
    {
        return $this->variable;
    }

    public function toExport(): string
    {
        $this->assertStorable($this->variable, __FUNCTION__);

        return VarExporter::export($this->variable);
    }

    public function toSerialize(): string
    {
        $this->assertStorable($this->variable, __FUNCTION__);

        return serialize($this->variable);
    }

    private function assertStorable(mixed $variable, string $callable): void
    {
        $this->assertIsNotResource($variable);
        if (is_object($variable)) {
            $this->breadcrumbObject($variable, $callable);
        } elseif (is_iterable($variable)) {
            $this->breadcrumbIterable($variable, $callable);
        }
    }

    /**
     * @throws UnableToStoreException
     */
    private function assertIsNotResource(mixed $variable): void
    {
        if (! is_resource($variable)) {
            return;
        }
        $message = $this->breadcrumb->count() > 0
            ? message('Argument contains a resource at %at%')
                ->withCode('%at%', $this->breadcrumb->__toString())
            : message('Argument is of type resource.');

        throw new UnableToStoreException($message);
    }

    /**
     * @param iterable<mixed, mixed> $variable
     * @throws UnableToStoreException
     * @throws OutOfBoundsException
     */
    private function breadcrumbIterable(iterable $variable, string $callable): void
    {
        $this->breadcrumb = $this->breadcrumb->withAdded('(iterable)');
        $iterableKey = $this->breadcrumb->pos();
        foreach ($variable as $key => $val) {
            $key = strval($key);
            $this->breadcrumb = $this->breadcrumb
                ->withAdded('key: ' . $key);
            $memberKey = $this->breadcrumb->pos();
            $this->assertStorable($val, $callable);
            $this->breadcrumb = $this->breadcrumb
                ->withRemoved($memberKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemoved($iterableKey);
    }

    private function breadcrumbObject(object $variable, string $callable): void
    {
        $this->breadcrumb = $this->breadcrumb
            ->withAdded('object: ' . $variable::class);
        $objectKey = $this->breadcrumb->pos();
        $reflection = new ReflectionObject($variable);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            /** @var ?ReflectionNamedType $namedType */
            $namedType = $property->getType();
            $propertyType = $namedType !== null
                ? ($namedType->getName() . ' ') : '';
            $propertyName = '$' . $property->getName();
            $this->breadcrumb = $this->breadcrumb->withAdded(
                <<<STRING
                property: {$propertyType}{$propertyName}
                STRING
            );
            $propertyKey = $this->breadcrumb->pos();
            // @infection-ignore-all
            $property->setAccessible(true);
            if ($property->isInitialized($variable)) {
                $this->assertStorable($property->getValue($variable), $callable);
            }
            $this->breadcrumb = $this->breadcrumb->withRemoved($propertyKey);
        }
        $this->breadcrumb = $this->breadcrumb->withRemoved($objectKey);
    }
}
