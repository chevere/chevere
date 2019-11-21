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

use Chevere\Components\Message\Message;
use Chevere\Components\Variable\Exceptions\VariableExportException;
use Chevere\Components\Variable\Exceptions\VariableIsResourceException;
use Chevere\Contracts\Variable\VariableExportContract;
use ReflectionObject;
use Throwable;

/**
 * Allows to interact with exportable variables.
 */
final class VariableExport implements VariableExportContract
{
    /** @var mixed */
    private $var;

    /** @var array Used to map the location of the validation */
    private $locator;

    /** @var array Contains the checked (ok) locator entries */
    private $check;

    /**
     * {@inheritdoc}
     */
    public function __construct($var)
    {
        $this->var = $var;
        $this->locator = [];
        $this->check = [];
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
            $this->locator[] = '(iterable)';
            $iterableKey = array_key_last($this->locator);
            foreach ($var as $key => $val) {
                $this->locator[] = 'key:' . $key;
                $memberKey = array_key_last($this->locator);
                $this->assertExportable($val);
                $this->check[] = $memberKey;
            }
            $this->check[] = $iterableKey;
        } elseif (is_object($var)) {
            $this->locator[] = 'object:' . get_class($var);
            $objectKey = array_key_last($this->locator);
            $reflection = new ReflectionObject($var);
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $this->locator[] = 'property:$' . $property->getName();
                $propertyKey = array_key_last($this->locator);
                $this->assertExportable($property->getValue($var));
                $this->check[] = $propertyKey;
            }
            $this->check[] = $objectKey;
        }
    }

    private function assertIsNotResource($var): void
    {
        if (is_resource($var)) {
            if (empty($this->locator)) {
                $message = new Message("Argument is a resource which can't be exported");
            } else {
                foreach ($this->check as $remove) {
                    unset($this->locator[$remove]);
                }
                $message = (new Message("Passed argument contains a resource which can't be exported at %locator%"))
                    ->code('%locator%', '[' . implode('][', $this->locator) . ']');
            }
            throw new VariableIsResourceException(
                $message->toString()
            );
        }
    }
}
