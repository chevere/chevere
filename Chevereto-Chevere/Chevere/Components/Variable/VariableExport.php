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

namespace Chevere\Components\Variable;

use Chevere\Components\Breadcrum\Breadcrum;
use Chevere\Components\Message\Message;
use Chevere\Components\Variable\Exceptions\VariableExportException;
use Chevere\Components\Variable\Exceptions\VariableIsResourceException;
use Chevere\Contracts\Variable\VariableExportContract;
use ReflectionObject;
use Throwable;
use Chevere\Contracts\Breadcrum\BreadcrumContract;

/**
 * Allows to interact with exportable variables.
 */
final class VariableExport implements VariableExportContract
{
    /** @var mixed */
    private $var;

    /** @var BreadcrumContract */
    private $breadcrum;

    /**
     * {@inheritdoc}
     */
    public function __construct($var)
    {
        $this->var = $var;
        $this->breadcrum = new Breadcrum();
        try {
            $this->assertExportable($this->var);
        } catch (Throwable $e) {
            throw new VariableExportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        return $this->var;
    }

    /**
     * {@inheritdoc}
     */
    public function toExport(): string
    {
        return var_export($this->var, true);
    }

    /**
     * {@inheritdoc}
     */
    public function toSerialize(): string
    {
        return serialize($this->var);
    }

    private function assertExportable($var): void
    {
        $this->assertIsNotResource($var);
        if (is_iterable($var)) {
            $this->breadcrum = $this->breadcrum
                ->withAddedItem('(iterable)');
            $iterableKey = $this->breadcrum->pos();
            foreach ($var as $key => $val) {
                $this->breadcrum = $this->breadcrum
                ->withAddedItem('key:' . $key);
                $memberKey = $this->breadcrum->pos();
                $this->assertExportable($val);
                $this->breadcrum = $this->breadcrum
                    ->withRemovedItem($memberKey);
            }
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($iterableKey);
        } elseif (is_object($var)) {
            $this->breadcrum = $this->breadcrum
                ->withAddedItem('object:' . get_class($var));
            $objectKey = $this->breadcrum->pos();
            $reflection = new ReflectionObject($var);
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $this->breadcrum = $this->breadcrum
                    ->withAddedItem('property:$' . $property->getName());
                $propertyKey = $this->breadcrum->pos();
                $this->assertExportable($property->getValue($var));
                $this->breadcrum = $this->breadcrum
                    ->withRemovedItem($propertyKey);
            }
            $this->breadcrum = $this->breadcrum
                    ->withRemovedItem($objectKey);
        }
    }

    private function assertIsNotResource($var): void
    {
        if (is_resource($var)) {
            if ($this->breadcrum->hasAny()) {
                $message = (new Message("Passed argument contains a resource which can't be exported at %at%"))
                    ->code('%at%', $this->breadcrum->toString());
            } else {
                $message = new Message("Argument is a resource which can't be exported");
            }
            throw new VariableIsResourceException(
                $message->toString()
            );
        }
    }
}
