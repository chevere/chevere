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

namespace Chevere\Http;

use JsonException;
use InvalidArgumentException;
use const Chevere\CLI;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Chevere\Message;

// use Chevere\Data\Data;

final class Response
{
    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    const DEFAULT_ENCODING_OPTIONS = JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

    /** @var int Bitmask */
    private $encodingOptions;

    /** @var string */
    private $jsonString;

    /** @var SymfonyResponse */
    private $symfony;

    public function __construct()
    {
        $this->symfony = new SymfonyResponse('', 200, []);
    }

    public function symfony(): symfonyResponse
    {
        return $this->symfony;
    }

    public function unsetContent(): void
    {
        $this->symfony->setContent(null);
    }

    public function setJsonContent(array $json): void
    {
        $this->setJsonHeaders();
        $this->setJsonString($json);
        $this->symfony->setContent($this->jsonString);
    }

    public function send(): SymfonyResponse
    {
        return $this->symfony()->send();
    }

    public function getStatusString(): string
    {
        return sprintf('HTTP/%s %s %s', $this->symfony->version, $this->symfony->statusCode, $this->symfony->statusText);
    }

    private function setJsonString(array $data): void
    {
        $this->encodingOptions = static::DEFAULT_ENCODING_OPTIONS;
        if (CLI) {
            $this->encodingOptions = $this->encodingOptions | JSON_PRETTY_PRINT;
        }
        $this->jsonString = $this->getJsonEncodedOutput($data);
        if (CLI) {
            $this->jsonString .= "\n";
        }
    }

    private function getJsonEncodedOutput(array $data): string
    {
        try {
            return json_encode($data, $this->encodingOptions, 512);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(
                (new Message('Unable to encode array as JSON (%m).'))
                    ->strtr('%m', $e->getMessage())
                    ->toString()
            );
        }
    }

    private function setJsonHeaders()
    {
        if (!$this->symfony->headers->has('Content-Type') || 'text/javascript' === $this->symfony->headers->get('Content-Type')) {
            $this->symfony->headers->set('Content-Type', 'application/vnd.api+json');
        }
    }
}
