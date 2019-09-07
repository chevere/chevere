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

use JsonException;
use InvalidArgumentException;
use Chevere\Message;
use const Chevere\CLI;

final class JsonApi
{
    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    const DEFAULT_ENCODING_OPTIONS = JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

    /** @var int Bitmask */
    private $encodingOptions;

    /** @var array The document’s “primary data” */
    private $data;

    /** @var array */
    private $array;

    /** @var Errors An array of error objects */
    private $errors;

    /** @var Included An array of resource objects that are related to the primary data and/or each other (“included resources”). */
    private $included;

    /** @var array Describes the server’s implementation */
    private $jsonapi;

    /** @var Links A links object related to the primary data. */
    private $links;

    /** @var Meta A meta object that contains non-standard meta-information. */
    private $meta;

    public function __construct()
    {
        $this->setEncodingOptions();
    }

    public function appendData(Data ...$data)
    {
        foreach ($data as $d) {
            $this->data[] = $d;
        }
    }

    public function toString(): string
    {
        $this->setString();
        return $this->string;
    }

    private function setString(): void
    {
        $this->setArray();
        $this->string = $this->getEncodedString();
    }

    private function setArray(): void
    {
        if (isset($this->data)) {
            $this->array['data'] = $this->getArray($this->data);
        }
    }

    private function getArray(array $array): array
    {
        $count = count($array);
        switch (true) {
            case 1 == $count:
                $return = $array[0]->toArray();

                break;
            default:
                foreach ($array as $object) {
                    $return[] = $object->toArray();
                }
                break;
        }
        return $return;
    }

    private function setEncodingOptions()
    {
        $this->encodingOptions = static::DEFAULT_ENCODING_OPTIONS;
        if (CLI) {
            $this->encodingOptions = $this->encodingOptions | JSON_PRETTY_PRINT;
        }
    }

    private function getEncodedString(): string
    {
        try {
            return json_encode($this->array, $this->encodingOptions, 512);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(
                (new Message('Unable to encode array as JSON (%m).'))
                    ->strtr('%m', $e->getMessage())
                    ->toString()
            );
        }
    }
}
