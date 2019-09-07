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

/**
 * The document’s “primary data” is a representation of the resource or collection of resources targeted by a request.
 *
 * Primary data MUST be either:
 * - a single resource object, a single resource identifier object, or null, for requests that target single resources
 * - an array of resource objects, an array of resource identifier objects, or an empty array ([]), for requests that target resource collections
 *
 * ! If a document does not contain a top-level data key, the included member MUST NOT be present either.
 * ! The members data and errors MUST NOT coexist in the same document.
 */
final class Data
{
    /** @var string */
    private $type;

    /** @var string */
    private $id;

    /** @var iterable */
    private $attributes;

    public function __construct(string $type, string $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function addAttribute(string $name, string $data): void
    {
        $this->attributes[$name] = $data;
    }

    public function toArray(): array
    {
        $return = [
            'type' => $this->type,
            'id' => $this->id,
        ];
        if (isset($this->attributes)) {
            $return['attributes'] = $this->attributes;
        }
        return $return;
    }
}
