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

use Exception;
use InvalidArgumentException;
use const Chevere\CLI;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Chevere\JsonApi\Data as JsonData;
use Chevere\Contracts\DataContract;
use Chevere\Data\Data;

final class Response
{
    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    // 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    const DEFAULT_ENCODING_OPTIONS = 15;

    private $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    /** @var bool */
    // private $hasBody = false;

    private $jsonString;

    private $callback;

    /** @var DataContract */
    private $data;

    // a meta object that contains non-standard meta-information.
    private $meta;

    /** @var SymfonyResponse */
    private $symfony;

    public function __construct()
    {
        $status = 200;
        $headers = [];
        $this->symfony = new SymfonyResponse('', $status, $headers);
    }

    public function symfony(): symfonyResponse
    {
        return $this->symfony;
    }

    public function hasData(): bool
    {
        return !empty($this->data);
    }

    public function getData(): array
    {
        return $this->data->toArray();
    }

    public function unsetContent(): void
    {
        $this->symfony->setContent(null);
        $this->data = null;
    }

    public function addData(JsonData $data): self
    {
        if (!$this->hasData()) {
            $this->data = new Data();
        }
        $this->data->append($data->toArray());

        return $this;
    }

    public function hasMeta(): bool
    {
        return !empty($this->meta);
    }

    public function setMeta(array $data): self
    {
        $this->meta = $data;

        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    /**
     * Returns options used while encoding data to JSON.
     *
     * @return int
     */
    public function getEncodingOptions()
    {
        return $this->encodingOptions;
    }

    /**
     * Sets options used while encoding data to JSON.
     *
     * @param int $encodingOptions
     *
     * @return $this
     */
    public function setEncodingOptions(int $encodingOptions): self
    {
        $this->encodingOptions = $encodingOptions;

        return $this;
    }

    public function setJsonContent(): self
    {
        if (null !== $this->callback) {
            return $this->symfony->setContent(sprintf('/**/%s(%s);', $this->callback, $this->jsonString));
        }

        $this->symfony->setContent($this->jsonString);

        return $this;
    }

    public function send(): SymfonyResponse
    {
        $this->setHasBody($this->hasData());
        if ($this->hasBody) {
            $this
                ->setJsonHeaders()
                ->setJsonString()
                ->setJsonContent();
        }

        return $this->symfony()->send();
    }

    public function setHasBody(bool $bool): self
    {
        $this->hasBody = $bool;

        return $this;
    }

    public function getStatusString(): string
    {
        return sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText);
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed $data
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    private function setJsonString(): self
    {
        $output = [
            // 'jsonapi' => $this->jsonapi,
            'meta' => $this->getMeta(),
            'data' => $this->getData(),
        ];
        // JSON_PRETTY_PRINT
        $encodingOptions = $this->encodingOptions;
        if (CLI) {
            $encodingOptions = $encodingOptions | JSON_PRETTY_PRINT;
        }

        $this->jsonString = $this->getJsonEncodedOutput($output, $encodingOptions);

        return $this;
    }

    private function getJsonEncodedOutput(array $data, int $encodingOptions): string
    {
        try {
            $json = json_encode($data, $encodingOptions);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new InvalidArgumentException(json_last_error_msg());
            }

            return $json;
        } catch (Exception $e) {
            if ('Exception' === get_class($e) && 0 === strpos($e->getMessage(), 'Failed calling ')) {
                throw $e->getPrevious() ?: $e;
            }
            throw $e;
        }
    }

    private function setJsonHeaders(): self
    {
        if (null !== $this->callback) {
            // Not using application/javascript for compatibility reasons with older browsers.
            $this->symfony->headers->set('Content-Type', 'text/javascript');

            return $this;
        }
        // Only set the header when there is none or when it equals 'text/javascript' (from a previous update with callback)
        // in order to not overwrite a custom definition.
        if (!$this->symfony->headers->has('Content-Type') || 'text/javascript' === $this->symfony->headers->get('Content-Type')) {
            $this->symfony->headers->set('Content-Type', 'application/vnd.api+json');
        }

        return $this;
    }
}
