<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\JsonApi;

use Chevere\Components\App\Instances\BootstrapInstance;
use InvalidArgumentException;
use JsonException;
use JsonSerializable;
use Chevere\Components\Message\Message;

final class EncodedDocument
{
    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    const DEFAULT_ENCODING_OPTIONS = JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

    private JsonSerializable $json;

    /** @var int Bitmask */
    private int $encodingOptions;

    public function __construct(JsonSerializable $json)
    {
        $this->json = $json;
    }

    public function toString(): string
    {
        $this->setEncodingOptions();

        return $this->getEncodedString();
    }

    private function setEncodingOptions(): void
    {
        $this->encodingOptions = static::DEFAULT_ENCODING_OPTIONS;
        if (BootstrapInstance::get()->isCli()) {
            $this->encodingOptions = $this->encodingOptions | JSON_PRETTY_PRINT;
        }
    }

    private function getEncodedString(): string
    {
        try {
            return json_encode($this->json, $this->encodingOptions, 1024);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(
                (new Message('Unable to encode array as JSON (%m)'))
                    ->strtr('%m', $e->getMessage())
                    ->toString()
            );
        }
    }
}
