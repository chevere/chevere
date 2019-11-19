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

namespace Chevere\Components\Serialize;

use Chevere\Components\Message\Message;
use Chevere\Contracts\Serialize\SerializeContract;
use InvalidArgumentException;
use ReflectionObject;

final class Serialize implements SerializeContract
{
    /** @var string */
    private $serialized;

    /** @var array Used to map the location of invalid resources */
    private $locator;

    /** @var array Contains the checked locator entries */
    private $check;

    /**
     * {@inheritdoc}
     */
    public function __construct($var)
    {
        $this->locator = [];
        $this->assertSerializable($var);
        $this->serialized = serialize($var);
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->serialized;
    }

    private function assertSerializable($var): void
    {
        $this->assertIsNotResource($var);
        if (is_iterable($var)) {
            $this->locator[] = '(iterable)';
            $iterableKey = array_key_last($this->locator);
            foreach ($var as $key => $var) {
                $this->locator[] = 'key:' . $key;
                $memberKey = array_key_last($this->locator);
                $this->assertSerializable($var);
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
                $this->assertSerializable($property->getValue($var));
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
            throw new InvalidArgumentException(
                $message->toString()
            );
        }
    }
}
