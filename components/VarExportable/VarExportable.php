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

use Chevere\Components\Breadcrum\Breadcrum;
use Chevere\Components\Breadcrum\Interfaces\BreadcrumInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\VarExportable\Exceptions\VarIsResourceException;
use Chevere\Components\VarExportable\Exceptions\VarNotExportableException;
use Chevere\Components\VarExportable\Interfaces\VarExportableInterface;
use ReflectionObject;
use Throwable;

/**
 * Allows to interact with exportable variables.
 */
final class VarExportable implements VarExportableInterface
{
    /** @var mixed */
    private $var;

    private BreadcrumInterface $breadcrum;

    /**
     * Creates a new instance.
     *
     * @psalm-suppress PossiblyInvalidArgument
     * @throws VarIsResourceException if $var contains resource
     */
    public function __construct($var)
    {
        $this->var = $var;
        $this->breadcrum = new Breadcrum();
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
            $this->breadcrumIterable($var);
        } elseif (is_object($var)) {
            $this->breadcrumObject($var);
        }
    }

    /**
     * @throws VarIsResourceException
     */
    private function assertIsNotResource($var): void
    {
        if (is_resource($var)) {
            if ($this->breadcrum->hasAny()) {
                $message = (new Message("Passed argument contains a resource which can't be exported at %at%"))
                    ->code('%at%', $this->breadcrum->toString());
            } else {
                $message = new Message("Argument is a resource which can't be exported");
            }
            throw new VarIsResourceException($message);
        }
    }

    /**
     * @throws VarIsResourceException
     */
    private function breadcrumIterable($var): void
    {
        $this->breadcrum = $this->breadcrum
            ->withAddedItem('(iterable)');
        $iterableKey = $this->breadcrum->pos();
        foreach ($var as $key => $val) {
            $key = (string) $key;
            $this->breadcrum = $this->breadcrum
                ->withAddedItem('key:' . $key);
            $memberKey = $this->breadcrum->pos();
            $this->assertExportable($val);
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($memberKey);
        }
        $this->breadcrum = $this->breadcrum
            ->withRemovedItem($iterableKey);
    }

    private function breadcrumObject(object $var): void
    {
        $this->breadcrum = $this->breadcrum
            ->withAddedItem('object:' . get_class($var));
        $objectKey = $this->breadcrum->pos();
        $reflection = new ReflectionObject($var);
        $properties = $reflection->getProperties();
        /**
         * @var \ReflectionProperty $property
         */
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $this->breadcrum = $this->breadcrum
                ->withAddedItem('property:$' . $property->getName());
            $propertyKey = $this->breadcrum->pos();
            if ($property->isInitialized($var)) {
                $this->assertExportable($property->getValue($var));
            }
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($propertyKey);
        }
        $this->breadcrum = $this->breadcrum
            ->withRemovedItem($objectKey);
    }
}
