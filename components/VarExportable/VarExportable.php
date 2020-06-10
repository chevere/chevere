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

namespace Chevere\Components\VarExportable;

use Chevere\Components\Breadcrumb\Breadcrumb;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\VarExportable\VarIsResourceException;
use Chevere\Exceptions\VarExportable\VarNotExportableException;
use Chevere\Interfaces\Breadcrumb\BreadcrumbInterface;
use Chevere\Interfaces\VarExportable\VarExportableInterface;
use ReflectionObject;
use ReflectionProperty;
use Throwable;

/**
 * Allows to interact with exportable variables.
 */
final class VarExportable implements VarExportableInterface
{
    /** @var mixed */
    private $var;

    private BreadcrumbInterface $breadcrumb;

    /**
     * @throws VarIsResourceException if $var contains resource
     */
    public function __construct($var)
    {
        $this->var = $var;
        $this->breadcrumb = new Breadcrumb();
        try {
            $this->assertExportable($this->var);
        } catch (Throwable $e) {
            throw new VarNotExportableException(
                new Message($e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    public function var()
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

    /**
     * @throws VarIsResourceException
     */
    private function assertExportable($var): void
    {
        $this->assertIsNotResource($var);
        if (is_iterable($var)) {
            $this->breadcrumbIterable($var);
        } elseif (is_object($var)) {
            $this->breadcrumbObject($var);
        }
    }

    /**
     * @throws VarIsResourceException
     */
    private function assertIsNotResource($var): void
    {
        if (is_resource($var)) {
            if ($this->breadcrumb->hasAny()) {
                $message = (new Message("Passed argument contains a resource which can't be exported at %at%"))
                    ->code('%at%', $this->breadcrumb->toString());
            } else {
                $message = new Message("Argument is a resource which can't be exported");
            }
            throw new VarIsResourceException($message);
        }
    }

    /**
     * @throws VarIsResourceException
     */
    private function breadcrumbIterable($var): void
    {
        $this->breadcrumb = $this->breadcrumb->withAddedItem('(iterable)');
        $iterableKey = $this->breadcrumb->pos();
        foreach ($var as $key => $val) {
            $key = (string) $key;
            $this->breadcrumb = $this->breadcrumb
                ->withAddedItem('key:' . $key);
            $memberKey = $this->breadcrumb->pos();
            $this->assertExportable($val);
            $this->breadcrumb = $this->breadcrumb
                ->withRemovedItem($memberKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemovedItem($iterableKey);
    }

    private function breadcrumbObject(object $var): void
    {
        $this->breadcrumb = $this->breadcrumb
            ->withAddedItem('object:' . get_class($var));
        $objectKey = $this->breadcrumb->pos();
        $reflection = new ReflectionObject($var);
        $properties = $reflection->getProperties();
        /**
         * @var ReflectionProperty $property
         */
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $this->breadcrumb = $this->breadcrumb
                ->withAddedItem('property:$' . $property->getName());
            $propertyKey = $this->breadcrumb->pos();
            if ($property->isInitialized($var)) {
                $this->assertExportable($property->getValue($var));
            }
            $this->breadcrumb = $this->breadcrumb
                ->withRemovedItem($propertyKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemovedItem($objectKey);
    }
}
