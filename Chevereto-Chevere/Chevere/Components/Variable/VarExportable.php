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

use Chevere\Components\Variable\Exceptions\VariableIsResourceException;
use Chevere\Contracts\Variable\VariableExportableContract;
use ReflectionObject;

/**
 * Allows to interact with variables.
 */
final class VariableExportable implements VariableExportableContract
{
    /** @var mixed */
    private $var;

    /** @var array Used to map the location of a invalid resource */
    private $locator;

    /** @var array Contains the checked locator entries */
    private $check;

    /**
     * {@inheritdoc}
     */
    public function __construct($var)
    {
        $this->var = $var;
        $this->locator = [];
        $this->check = [];
        $this->assertExportable($this->var);
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
    public function toExport()
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
            foreach ($var as $key => $var) {
                $this->locator[] = 'key:' . $key;
                $memberKey = array_key_last($this->locator);
                $this->assertExportable($var);
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
                $message = new Message("Argument is a resource which can't be serialized");
            } else {
                foreach ($this->check as $remove) {
                    unset($this->locator[$remove]);
                }
                $message = (new Message("Passed argument contains a resource which can't be serialized at %locator%"))
                    ->code('%locator%', '[' . implode('][', $this->locator) . ']');
            }
            throw new VariableIsResourceException(
                $message->toString()
            );
        }
    }
}
