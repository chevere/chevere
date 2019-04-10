<?php

declare(strict_types=1);

namespace App;

use Chevereto\Core\Traits\DecoratedConstructorTrait;

class User
{
    use DecoratedConstructorTrait;

    protected $description = 'User id.';
    protected $regex = '[0-8]+';

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->name = 'NaMe';
    }

    public function __toString()
    {
        return $this;
    }
}
