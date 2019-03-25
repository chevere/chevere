<?php

declare(strict_types=1);

namespace App;

class User
{
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
