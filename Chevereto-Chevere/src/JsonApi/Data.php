<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\JsonApi;

use LogicException;

/**
 * JSON:API Data.
 */
class Data
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $type;

    /** @var array */
    protected $attributes;

    /** @var bool */
    protected $isValidated;

    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function addAttribute(string $attributeName, string $data): self
    {
        $this->attributes[$attributeName] = $data;

        return $this;
    }

    public function validate()
    {
        if (!isset($this->id)) {
            throw new LogicException('Missing id parameter.');
        }
        if (!isset($this->type)) {
            throw new LogicException('Missing type parameter.');
        }
        $this->isValidated = true;
    }

    public function isValidated(): bool
    {
        return (bool) $this->isValidated;
    }

    public function toArray(): array
    {
        if (!isset($this->isValidated)) {
            $this->validate();
        }

        return [
            'type' => $this->type,
            'id' => $this->id,
            'attributes' => $this->attributes,
            // 'description' => $this->description,
            // 'data' => $this->description,
        ];
    }
}
