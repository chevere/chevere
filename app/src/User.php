<?php

declare(strict_types=1);

namespace App;

use LogicException;
use Chevere\Message\Message;
use Chevere\FromString; // Sets the base string descriptor ($stringDescription, $stringRegex)
use Chevere\Interfaces\CreateFromString; // Exposes that has ::createFromString

class User extends FromString implements CreateFromString
{
    /** {@inheritdoc} */
    protected static $stringDescription = 'Username';

    /** {@inheritdoc} */
    protected static $stringRegex = '[a-z]+';

    /** @var string The string argument passed in ::createFromString() */
    protected $fromStringArgument;

    /** @var bool True if the object was contructed passing an argument */
    protected $hasConstructArgument;

    /**
     * @param int $id The user id
     */
    public function __construct(int $id = null)
    {
        if (isset($id)) {
            $this->hasConstructArgument = true;
            // DB HANDLE
        } else {
            $this->hasConstructArgument = false;
        }
    }

    /**
     * @param string Username
     *
     * @see FromString, $stringDescription, $stringRegex
     */
    public function createFromString(string $string): CreateFromString
    {
        $this->assertNoConstructArgument();
        $this->assertFromString($string);
        $this->fromStringArgument = $string;

        return $this;
    }

    /**
     * Throws a LogicException if the class was constructed with an argument.
     */
    protected function assertNoConstructArgument(): void
    {
        if ($this->hasConstructArgument) {
            throw new LogicException(
                (new Message('An instance of %s has been already created (WHERE?).'))
                    ->code('%s', __CLASS__)
                    ->toString()
            );
        }
    }
}
